<?php

namespace App\Controller;
use App\Entity\Personne;
use App\Repository\ArbreRepository;
use App\Repository\PersonneRepository;
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
    public function index(Request $request, ArbreRepository $arbreRepository, EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $entityManager->createQueryBuilder();
        
        $personneRepository = $this->entityManager->getRepository(Personne::class);

        $user = $this->getUser();
        $userId = $user->getId();

        $query = $queryBuilder
            ->select('p.id')
            ->from('App\Entity\Personne', 'p')
            ->where('p.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        $personId = $query->getSingleScalarResult();
        // dd( $personId);
        $personne = $personneRepository->find($personId);

        $ancestors = $arbreRepository->getAncestors($personId);

        return $this->render('edit_tree/index.html.twig', [
             'personne' => $personne,
             'ancestors' => $ancestors
        ]);
    }
}
