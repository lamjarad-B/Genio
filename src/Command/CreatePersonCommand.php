<?php

namespace App\Command;

use App\Entity\Personne;
use App\Entity\Relation;
use App\Entity\TypeRelation;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create:person',
    description: 'Add a short description for your command',
)]
class CreatePersonCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
       
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {   
       $enfant = (new Personne())->setNom("MARTIN")->setPrenom("Jean SELIQUET")->setDateNaissance(new DateTime('1809-01-01'))->setSexe("M");
       $pere = (new Personne())->setNom("MARTIN")->setPrenom("Jean Antoine Pellique")->setDateNaissance(new DateTime('1760-01-01'))->setSexe("M");
       $mere = (new Personne())->setNom("MOTTE")->setPrenom("Magdeleine")->setDateNaissance(new DateTime('1761-01-01'))->setSexe("F");

        //Parents de Jean Antoine Pellique MARTIN
       $pere2 = (new Personne())->setNom("MARTIN")->setPrenom("Louis")->setDateNaissance(new DateTime('1732-01-01'))->setSexe("M");
       $mere2 = (new Personne())->setNom("MARCELIN")->setPrenom("Marguerite")->setDateNaissance(new DateTime('1731-01-01'))->setSexe("F");

        //Parents de Louis MARTIN
       $pere3 = (new Personne())->setNom("MARTIN")->setPrenom("Louis 2")->setDateNaissance(new DateTime('1695-01-01'))->setSexe("M");
       $mere3 = (new Personne())->setNom("MAUMOINIER")->setPrenom("Marie")->setDateNaissance(new DateTime('1694-01-01'))->setSexe("F");
       
        //Parents Marguerite MARCELIN 1731-1795 	  	 
       $pere3B = (new Personne())->setNom("MARCELIN")->setPrenom("Christophe")->setDateNaissance(new DateTime('1681-01-01'))->setSexe("M");
       $mere3B = (new Personne())->setNom("RIGAUD")->setPrenom("Jeanne")->setDateNaissance(new DateTime('1690-01-01'))->setSexe("F");


       $pereRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"père"]);
       $enfantRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"enfant"]);
       $mereRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"mère"]);

       $relation1 = (new Relation())->setPersonne1($pere)->setRelationType($pereRelation)->setPersonne2($enfant);
       $relation2 = (new Relation())->setPersonne2($pere)->setRelationType($enfantRelation)->setPersonne1($enfant);
       $relation3 = (new Relation())->setPersonne1($mere)->setRelationType($mereRelation)->setPersonne2($enfant);
       $relation4 = (new Relation())->setPersonne2($mere)->setRelationType($enfantRelation)->setPersonne1($enfant);

       $relation5 = (new Relation())->setPersonne1($pere2)->setRelationType($pereRelation)->setPersonne2($pere);
       $relation6 = (new Relation())->setPersonne2($pere2)->setRelationType($enfantRelation)->setPersonne1($pere);
       $relation7 = (new Relation())->setPersonne1($mere2)->setRelationType($mereRelation)->setPersonne2($pere);
       $relation8 = (new Relation())->setPersonne2($mere2)->setRelationType($enfantRelation)->setPersonne1($pere);

       $relation9 = (new Relation())->setPersonne1($pere3)->setRelationType($pereRelation)->setPersonne2($pere2);
       $relation10 = (new Relation())->setPersonne2($pere3)->setRelationType($enfantRelation)->setPersonne1($pere2);
       $relation11 = (new Relation())->setPersonne1($mere3)->setRelationType($mereRelation)->setPersonne2($pere2);
       $relation12 = (new Relation())->setPersonne2($mere3)->setRelationType($enfantRelation)->setPersonne1($pere2);

       $relation13 = (new Relation())->setPersonne1($pere3B)->setRelationType($pereRelation)->setPersonne2($mere2);
       $relation14 = (new Relation())->setPersonne2($pere3B)->setRelationType($enfantRelation)->setPersonne1($mere2);
       $relation15 = (new Relation())->setPersonne1($mere3B)->setRelationType($mereRelation)->setPersonne2($mere2);
       $relation16 = (new Relation())->setPersonne2($mere3B)->setRelationType($enfantRelation)->setPersonne1($mere2);
       
       $this->entityManager->persist($enfant);
       $this->entityManager->persist($pere);
       $this->entityManager->persist($mere);
       $this->entityManager->persist($pere2);
       $this->entityManager->persist($mere2);
       $this->entityManager->persist($mere3);
       $this->entityManager->persist($pere3);
       $this->entityManager->persist($mere3B);
       $this->entityManager->persist($pere3B);

       $this->entityManager->persist($relation1);
       $this->entityManager->persist($relation2);
       $this->entityManager->persist($relation3);
       $this->entityManager->persist($relation4);
       $this->entityManager->persist($relation5);
       $this->entityManager->persist($relation6);
       $this->entityManager->persist($relation7);
       $this->entityManager->persist($relation8);
       $this->entityManager->persist($relation9);
       $this->entityManager->persist($relation10);
       $this->entityManager->persist($relation11);
       $this->entityManager->persist($relation12);
       $this->entityManager->persist($relation13);
       $this->entityManager->persist($relation14);
       $this->entityManager->persist($relation15);
       $this->entityManager->persist($relation16);

        $this->entityManager->flush();



        return Command::SUCCESS;
    }
}
