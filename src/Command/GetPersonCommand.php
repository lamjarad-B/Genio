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
    name: 'app:get:person',
    description: 'Add a short description for your command',
)]
class GetPersonCommand extends Command
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
        $pere = $this->entityManager->getRepository(Personne::class)->findOneBy(["nom"=>"Brahim"]);
        $enfant = $this->entityManager->getRepository(Personne::class)->findOneBy(["nom"=>"Florian"]);

        $relationPere = $pere->getRelationsPersonne1();
        dd($relationPere[0]);

        return Command::SUCCESS;
    }
}
