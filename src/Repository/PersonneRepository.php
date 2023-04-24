<?php

namespace App\Repository;

use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
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



    // public function getAncestors(Connection $connection, int $personId): array
    // {
    //     $queryBuilder = $connection->createQueryBuilder();
        
    //     $queryBuilder->select('p.id', 'p.nom', 'p.prenom', 'p.date_naissance', 'p.date_deces', 'p.sexe')
    //                 ->from('Personne', 'p')
    //                 ->join('p', 'Relations', 'r', 'p.id = r.personne1_id OR p.id = r.personne2_id')
    //                 ->join('r', 'TypeRelation', 't', 'r.relation_type_id = t.id')
    //                 ->where('t.nom_relation IN (:pere, :mere)')
    //                 ->andWhere('p.id <> :personId')
    //                 ->setParameter('pere', 'père')
    //                 ->setParameter('mere', 'mère')
    //                 ->setParameter('personId', $personId);
        
    //     $results = $queryBuilder->execute()->fetchAllAssociative();
        
    //     $ancestors = array();
        
    //     foreach ($results as $result) {
    //         $person = array(
    //             'id' => $result['id'],
    //             'nom' => $result['nom'],
    //             'prenom' => $result['prenom'],
    //             'date_naissance' => $result['date_naissance'],
    //             'date_deces' => $result['date_deces'],
    //             'sexe' => $result['sexe']
    //         );
    //         array_push($ancestors, $person);
    //     }
        
    //     return $ancestors;
    // }



}
