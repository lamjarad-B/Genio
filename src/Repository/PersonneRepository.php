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

    public function findIfExist(?string $id, ){
        $qb = $this->createQueryBuilder('p');
        $qb ->andWhere('p.idGedcom = :idGedcom')
            ->setParameter('idGedcom', $id);

        return  $qb->getQuery()->getResult();

    }
    public function findForRelation(?string $id) {
        $qb = $this->createQueryBuilder('p');
        $qb//->select('p.id')
            ->andWhere('p.idGedcom = :idGedcom')
            ->setParameter('idGedcom', $id);
        return $qb->getQuery()->getResult();
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

    public function addPartner(
        int $personId,
        string $nom_conjoint,
        string $prenom_conjoint,
        string $sexe_conjoint,
        ?DateTime $date_naissance_conjoint,
        ?DateTime $date_deces_conjoint,
        ?string $lieu_naissance_conjoint,
        ){
        $person = $this->entityManager->getRepository(Personne::class)->find($personId);
        //dd($person);
        $conjoint = (new Personne())->setNom($nom_conjoint)->setPrenom($prenom_conjoint)->setDateNaissance($date_naissance_conjoint)->setDateDeces($date_deces_conjoint)->setSexe($sexe_conjoint)->setLieuNaissance($lieu_naissance_conjoint);

        $conjointRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"conjoint(e)"]);

        $relation = (new Relation())->setPersonne1($person)->setRelationType($conjointRelation)->setPersonne2($conjoint);

        $this->entityManager->persist($conjoint);

        $this->entityManager->persist($relation);


        $this->entityManager->flush();


    }

	public function checkPerson(
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
        //$entityManager = $this->getDoctrine()->getManager();
        $personneRepository = $this->entityManager->getRepository(Personne::class);

        $queryBuilder = $personneRepository->createQueryBuilder('p');
        $queryBuilder->leftJoin('p.relationsPersonne1', 'r')
			->leftJoin('r.personne1', 'rt')
			->where('p.nom = :nom')
			->andWhere('p.prenom = :prenom')
			->setParameter('nom', $nom)
			->setParameter('prenom', $prenom);

         // Vérification du conjoint
        $queryBuilder->andWhere($queryBuilder->expr()->neq('rt.id', ':relationTypeId'))
			->setParameter('relationTypeId', 4)
			->setMaxResults(1); // Limite le résultat à un seul enregistrement

        $existingPerson = $queryBuilder->getQuery()->getResult();

        if (empty($existingPerson)) {
            if ($date_naissance !== null) {
                $queryBuilder->andWhere('p.dateNaissance = :dateNaissance')
                    ->setParameter('dateNaissance', $date_naissance);
            }

            if ($date_deces !== null) {
                $queryBuilder->andWhere('p.dateDeces = :dateDeces')
                    ->setParameter('dateDeces', $date_deces);
            }

            if ($lieu_naissance !== null) {
                $queryBuilder->andWhere('p.lieu_naissance = :lieu_naissance')
                    ->setParameter('lieu_naissance', $lieu_naissance);
            }
            $queryBuilder->orWhere('p.nom = :nomMere')
            ->orWhere('p.prenom = :prenomMere')
            ->setParameter('nomMere', $nomMere)
            ->setParameter('prenomMere', $prenomMere);

            if ($date_naissance_mere !== null) {
                $queryBuilder->andWhere('p.dateNaissance = :date_naissance_mere')
                    ->setParameter('date_naissance_mere', $date_naissance_mere);
            }

            if ($date_deces_mere !== null) {
                $queryBuilder->andWhere('p.dateDeces = :date_deces_mere')
                    ->setParameter('date_deces_mere', $date_deces_mere);
            }

            if ($lieu_naissance_mere !== null) {
                $queryBuilder->andWhere('p.lieu_naissance = :lieu_naissance_mere')
                    ->setParameter('lieu_naissance_mere', $lieu_naissance_mere);
            }

            return $queryBuilder->getQuery()->getResult();
        }

		return $existingPerson;
    }
}