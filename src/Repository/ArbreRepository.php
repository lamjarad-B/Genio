<?php

namespace App\Repository;

use App\Entity\Personne;
use App\Entity\Relation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;


class ArbreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }

    public function getAncestors(Connection $connection, int $personId): array
    {
        $queryBuilder = $connection->createQueryBuilder();
        
        $queryBuilder->select('p.id', 'p.nom', 'p.prenom', 'p.date_naissance', 'p.date_deces', 'p.sexe')
                    ->from('personne', 'p')
                    ->join('p', 'relation', 'r', '(p.id = r.personne1_id OR p.id = r.personne2_id)')
                    ->join('r', 'type_relation', 't', 'r.relation_type_id = t.id')
                    ->where('t.nom_relation IN (:pere, :mere)')
                    ->andWhere('p.id <> :personId')
                    ->setParameter('pere', 'père')
                    ->setParameter('mere', 'mère')
                    ->setParameter('personId', $personId);
        
        $results = $queryBuilder->executeQuery()->fetchAllAssociative();
        
        $ancestors = array();
        
        foreach ($results as $result) {
            $person = array(
                'id' => $result['id'],
                'nom' => $result['nom'],
                'prenom' => $result['prenom'],
                'dateNaissance' => $result['date_naissance'],
                'dateDeces' => $result['date_deces'],
                'sexe' => $result['sexe']
            );
            array_push($ancestors, $person);
        }
        
        return $ancestors;
    }
}
