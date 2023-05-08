<?php

namespace App\Repository;

use App\Entity\Personne;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


class PersonneRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Personne::class);
        $this->entityManager = $entityManager;
    }

    public function findOneBy(array $criteria, array $orderBy = null)
    {
        $entityManager = $this->getEntityManager();
        $repository = $entityManager->getRepository(Personne::class);

        return $repository->findOneBy($criteria, $orderBy);
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

    public function createTree(
        string $nom, 
        string $prenom, 
        ?DateTime $date_naissance, 
        ?DateTime $date_deces, 
        ?string $lieu_naissance, 
        string $nomMere, 
        string $prenomMere, 
        ?DateTime $date_naissance_mere, 
        ?DateTime $date_deces_mere, 
        ?string $lieu_naissance_mere
        ){
       $pere = (new Personne())->setNom($nom)->setPrenom($prenom)->setDateNaissance($date_naissance)->setSexe("M")->setDateDeces($date_deces)->setLieuNaissance($lieu_naissance);
       $mere = (new Personne())->setNom($nomMere)->setPrenom($prenomMere)->setDateNaissance($date_naissance_mere)->setSexe("F")->setDateDeces($date_deces_mere)->setLieuNaissance($lieu_naissance_mere);

       $this->entityManager->persist($pere);
       $this->entityManager->persist($mere);
        
       $this->entityManager->flush();
       

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
