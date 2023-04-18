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
       $pere = (new Personne())->setNom("Brahim")->setPrenom("Lam")->setDateNaissance(new DateTime())->setSexe("M");
       $enfant = (new Personne())->setNom("Florian")->setPrenom("Trayon")->setDateNaissance(new DateTime())->setSexe("M");

       $pereRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"père"]);
       $enfantRelation = $this->entityManager->getRepository(TypeRelation::class)->findOneBy(["nom_relation"=>"enfant"]);

       $relation1 = (new Relation())->setPersonne1($pere)->setPersonne2($enfant)->setRelationType($pereRelation);
       $relation2 = (new Relation())->setPersonne2($pere)->setPersonne1($enfant)->setRelationType($enfantRelation);

       $this->entityManager->persist($pere);
       $this->entityManager->persist($enfant);
       $this->entityManager->persist($relation1);
       $this->entityManager->persist($relation2);

        $this->entityManager->flush();



        return Command::SUCCESS;
    }
}
