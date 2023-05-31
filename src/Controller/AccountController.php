<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\User;

use App\Repository\ArbreRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/account', name: 'app_account')]
    public function index(UserRepository $userRepository, ArbreRepository $arbreRepository, EntityManagerInterface $entityManager): Response
    {   
        $personneRepository = $this->entityManager->getRepository(Personne::class);
        
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
        //dd( $personId);
        $personne = $personneRepository->find($personId);
        //dd( $personne);

        $ancestors = $arbreRepository->getAncestors($personId);

        return $this->render('account/index.html.twig', [
            'personne' => $personne,
             'ancestors' => $ancestors,
             'originId' => $personId,
             'user' => $user

        ]);
    }
}
