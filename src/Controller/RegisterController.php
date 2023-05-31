<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegisterController extends AbstractController
{
    private $entityManager;
    private $tokenStorage;

    public function __construct(entityManagerInterface $entityManager, TokenStorageInterface $tokenStorage){
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/register', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $cnx = "Connexion";
        }
        else{
            $cnx = "Déconnexion";
        }
        $user = new User();
        $form = $this->createForm( RegisterType::class, $user);
        $form->handleRequest($request);// Ecouter la requette entrante (l'objet request)

        if($form->isSubmitted() && $form->isValid()){// est-ce que mon formulaire a été soumis
            $user = $form->getData();
            $password = $passwordHasher->hashPassword($user, $user->getPassword());//cette methode prend 2 paramètre: le 1er c'est l'objet $user et le 2eme c'est le mot de passe saisi
            $user->setPassword($password); // Reinjecte le mot de passe encoder
            //dd($user); //comme var_dump

            //$doctrine = $this->getDoctrine()->getManager(); //appel de doctrine
            $this->entityManager->persist($user);// Figer la data/ entityManager c'est le constructeur
            $this->entityManager->flush(); // Enregistrer la data dans la base de données
            
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->tokenStorage->setToken($token);
    
            // Redirigez l'utilisateur vers une autre page après la connexion
            $targetUrl = $this->generateUrl('app_create_tree'); // Remplacez par le nom de la route de la page de destination
    
            return new RedirectResponse($targetUrl);
        }

        return $this->render('register/index.html.twig',
        [
            'form' => $form->createView(), // appeler la variable $form et creer la view de ce formulaire
            'cnx' => $cnx,
            'user' => $user
        ]);
    }
}
