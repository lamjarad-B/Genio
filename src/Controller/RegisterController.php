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

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(entityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/register', name: 'app_register')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
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
        }

        return $this->render('register/index.html.twig',
        [
            'form' => $form->createView() // appeler la variable $form et creer la view de ce formulaire
        ]);
    }
}
