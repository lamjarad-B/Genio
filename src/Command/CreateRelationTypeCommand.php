<?php

namespace App\Command;

use App\Entity\TypeRelation;
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
    name: 'app:create:relationType',
    description: 'Add a short description for your command',
)]
class CreateRelationTypeCommand extends Command
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
        // $pere = (new TypeRelation())->setNomRelation("père");
        // $this->entityManager->persist($pere);
        $conjoint = (new TypeRelation())->setNomRelation("conjoint(e)");
        $this->entityManager->persist($conjoint);
        $fraterie = (new TypeRelation())->setNomRelation("fraterie");
        $this->entityManager->persist($fraterie);

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
