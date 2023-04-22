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

    public function findBySearchCriteria(object $criteria, ?string $nomConjoint, ?string $prenomConjoint, ?int $limit = null)
    {
        $qb = $this->createQueryBuilder('p');

        if ($criteria->getNom() !== null) {
            $qb->andWhere('p.nom LIKE :nom')
                ->setParameter('nom', '%' . $criteria->getNom() . '%');
        }

        if ($criteria->getPrenom() !== null) {
            $qb->andWhere('p.prenom LIKE :prenom')
                ->setParameter('prenom', '%' . $criteria->getPrenom() . '%');
        }

        if ($criteria->getDateNaissance() !== null) {
            $qb->andWhere('p.date_naissance = :date_naissance')
                ->setParameter('date_naissance', $criteria->getDateNaissance());
        }

        if ($nomConjoint !== null) {
            $qb->leftJoin('p.relationsPersonne1', 'r')
                ->leftJoin('r.personne2', 'p2')
                ->orWhere('p2.nom LIKE :nomConjoint')
                ->setParameter('nomConjoint', '%' . $nomConjoint . '%');
        }

        if ($prenomConjoint !== null) {
            $qb->leftJoin('p.relationsPersonne1', 'r')
                ->leftJoin('r.personne2', 'p2')
                ->orWhere('p2.prenom LIKE :prenomConjoint')
                ->setParameter('prenomConjoint', '%' . $prenomConjoint . '%');
        }

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

}
