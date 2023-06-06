<?php

namespace App\Repository;

use App\Entity\Personne;
use App\Entity\Relation;
use App\Entity\TypeRelation;
use App\Entity\User;
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
        string $nomProprietaire,
        string $prenomProprietaire,
        string $sexe,
        ?DateTime $date_naissance_Proprietaire,
        //?DateTime $date_deces_Proprietaire,
        ?string $lieu_naissance_Proprietaire,
        ?int $userId,
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
        $user = $this->entityManager->getRepository(User::class)->find($userId);
       $propietaire = (new Personne())->setNom($nomProprietaire)->setPrenom($prenomProprietaire)->setDateNaissance($date_naissance_Proprietaire)->setSexe($sexe)->setLieuNaissance($lieu_naissance_Proprietaire)->setUser($user);
       $pere = (new Personne())->setNom($nom)->setPrenom($prenom)->setDateNaissance($date_naissance)->setSexe('M')->setDateDeces($date_deces)->setLieuNaissance($lieu_naissance);
       $mere = (new Personne())->setNom($nomMere)->setPrenom($prenomMere)->setDateNaissance($date_naissance_mere)->setSexe("F")->setDateDeces($date_deces_mere)->setLieuNaissance($lieu_naissance_mere);

       $pereRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"père"]);
       $enfantRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"enfant"]);
       $mereRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"mère"]);

       $relation1 = (new Relation())->setPersonne1($pere)->setRelationType($pereRelation)->setPersonne2($propietaire);
       $relation2 = (new Relation())->setPersonne2($pere)->setRelationType($enfantRelation)->setPersonne1($propietaire);
       $relation3 = (new Relation())->setPersonne1($mere)->setRelationType($mereRelation)->setPersonne2($propietaire);
       $relation4 = (new Relation())->setPersonne2($mere)->setRelationType($enfantRelation)->setPersonne1($propietaire);

       $this->entityManager->persist($propietaire);

       $this->entityManager->persist($pere);
       $this->entityManager->persist($mere);
       $this->entityManager->persist($relation1);
       $this->entityManager->persist($relation2);
       $this->entityManager->persist($relation3);
       $this->entityManager->persist($relation4);

       $this->entityManager->flush();


    }

    public function addAncetors(
        int $personId,
        ?string $nom,
        ?string $prenom,
        ?DateTime $date_naissance,
        ?DateTime $date_deces,
        ?string $lieu_naissance,
        ?string $nomMere,
        ?string $prenomMere,
        ?DateTime $date_naissance_mere,
        ?DateTime $date_deces_mere,
        ?string $lieu_naissance_mere
    )
	{
		$enfant = $this->entityManager->getRepository(Personne::class)->find($personId);

		if ($nom)
		{
			$pere = (new Personne())->setNom($nom)->setPrenom($prenom)->setDateNaissance($date_naissance)->setSexe("M")->setDateDeces($date_deces)->setLieuNaissance($lieu_naissance);
		}

		if ($nomMere)
		{
			$mere = (new Personne())->setNom($nomMere)->setPrenom($prenomMere)->setDateNaissance($date_naissance_mere)->setSexe("F")->setDateDeces($date_deces_mere)->setLieuNaissance($lieu_naissance_mere);
		}

		$enfantRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"enfant"]);

		if (!empty($pere))
		{
			$pereRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"père"]);
			$relation1 = (new Relation())->setPersonne1($pere)->setRelationType($pereRelation)->setPersonne2($enfant);
			$relation2 = (new Relation())->setPersonne2($pere)->setRelationType($enfantRelation)->setPersonne1($enfant);

			$this->entityManager->persist($pere);
			$this->entityManager->persist($relation1);
			$this->entityManager->persist($relation2);
		}

		if (!empty($mere))
		{
			$mereRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"mère"]);
			$relation3 = (new Relation())->setPersonne1($mere)->setRelationType($mereRelation)->setPersonne2($enfant);
			$relation4 = (new Relation())->setPersonne2($mere)->setRelationType($enfantRelation)->setPersonne1($enfant);

			$this->entityManager->persist($mere);
			$this->entityManager->persist($relation3);
			$this->entityManager->persist($relation4);
		}

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
