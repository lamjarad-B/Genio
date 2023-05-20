<?php

namespace App\Controller;
use App\Entity\Personne;
use App\Repository\ArbreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class EditTreeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/editTree', name: 'app_edit_tree')]
    public function index(Request $request, ArbreRepository $arbreRepository): Response
    {
        $personneRepository = $this->entityManager->getRepository(Personne::class);
        $user = $this->getUser();
        //dd($user);
        //$personId = $request->get('id');
        $personId = $personneRepository->findOneBy([])->getId();
        dump($personId);
        $personne = $personneRepository->find($personId);
        
        $ancestors = $arbreRepository->getAncestors($personId);

        return $this->render('edit_tree/index.html.twig', [
            'personne' => $personne,
            'ancestors' => $ancestors
        ]);
    }
}
