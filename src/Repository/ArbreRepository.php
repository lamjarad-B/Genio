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

    public function getAncestors(int $personId, array &$ancestors = []): array
{
    $connection = $this->getEntityManager()->getConnection();
    $sql = 'SELECT DISTINCT p.id, p.nom, p.prenom, p.date_naissance, p.date_deces, p.sexe, p.lieu_naissance, r.personne1_id, r.relation_type_id, r.personne2_id
            FROM personne p
            LEFT JOIN relation r ON p.id IN (r.personne1_id, r.personne2_id) AND r.relation_type_id IN (SELECT id FROM type_relation WHERE nom_relation IN (:pere, :mere))
            WHERE p.id = :personId
            OR p.id IN (
                SELECT personne1_id
                FROM relation
                WHERE personne2_id = :personId
                AND relation_type_id IN (SELECT id FROM type_relation WHERE nom_relation IN (:pere, :mere))
            )
            OR p.id IN (
                SELECT personne2_id
                FROM relation
                WHERE personne1_id = :personId
                AND relation_type_id IN (SELECT id FROM type_relation WHERE nom_relation IN (:pere, :mere))
            )
            AND p.sexe IN (:pere, :mere)
            GROUP BY p.id';

    $statement = $connection->prepare($sql);
    $results= $statement->executeQuery(['personId' => $personId, "pere" => "père", "mere" => "mère"]);

    $tmp = $results->fetchAllAssociative();

    foreach ($tmp as $ancestor) {
        if (!in_array($ancestor, $ancestors)) {
            $ancestors[] = $ancestor;
            $this->getAncestors($ancestor['id'], $ancestors);
        }
    }

    return $ancestors;
}



}
