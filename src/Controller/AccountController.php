<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(UserRepository $userRepository): Response
    {   
        $user = $this->getUser();
        //on récupère l'ID de l'utilisateur connecté
        $userId = $this->getUser()->getId();
        // on récupère l'arbre généalogique de l'utilisateur connecté
        $arbreGenealogique = $userRepository->getArbreGenealogique($userId);

        return $this->render('account/index.html.twig', [
            'controller_name' => 'AccountController',
            'arbreGenealogique' => $arbreGenealogique,
            'user'=> $user,

        ]);
    }
}
