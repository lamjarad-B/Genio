<?php

namespace App\Repository;

use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }

    public function findBySearchCriteria(array $criteria)
    {
        $qb = $this->createQueryBuilder('p');

        if (isset($criteria['nom'])) {
            $qb->andWhere('p.nom LIKE :nom')
                ->setParameter('nom', '%' . $criteria['nom'] . '%');
        }
    
        if (isset($criteria['prenom'])) {
            $qb->andWhere('p.prenom LIKE :prenom')
                ->setParameter('prenom', '%' . $criteria['prenom'] . '%');
        }
    
        if (isset($criteria['date_naissance'])) {
            $qb->andWhere('p.date_naissance = :date_naissance')
                ->setParameter('date_naissance', $criteria['date_naissance']);
        }

        // if (isset($criteria['nomConjoint'])) {
        //     $qb->leftJoin('p.relationsPersonne1', 'r')
        //         ->leftJoin('r.personne2', 'p2')
        //         ->orWhere('p2.nom LIKE :nomConjoint')
        //         ->setParameter('nomConjoint', '%' . $criteria['nomConjoint'] . '%');
        // }

        // if (isset($criteria['prenomConjoint'])) {
        //     $qb->leftJoin('p.relationsPersonne1', 'r')
        //         ->leftJoin('r.personne2', 'p2')
        //         ->orWhere('p2.prenom LIKE :prenomConjoint')
        //         ->setParameter('prenomConjoint', '%' . $criteria['prenomConjoint'] . '%');
        // }

        return $qb->getQuery()->getResult();
    }


}
