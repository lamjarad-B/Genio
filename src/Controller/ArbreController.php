<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Repository\ArbreRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;

class ArbreController extends AbstractController
{   
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/arbre', name: 'app_arbre')]
    public function index(Connection $connection, Request $request, ArbreRepository $arbreRepository): Response
    {
        $personneRepository = $this->entityManager->getRepository(Personne::class);
        $personId = $request->get('id');
        $personne = $personneRepository->find($personId);
        
        $ancestors = $arbreRepository->getAncestors($connection, $personId);
        //dd($ancestors);
        
        return $this->render('arbre/index.html.twig', [
            'personne' => $personne,
            'ancestors' => $ancestors
        ]);
    }
}
