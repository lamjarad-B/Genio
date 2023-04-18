<?php 
namespace App\Command;

use App\Entity\Personne;
use App\Entity\Relation;
use App\Entity\TypeRelation;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
class CreatePerson extends Command{
    protected static $defaultName = 'app:create:personne';
    public function __construct(private EntityManagerInterface $entityManager )
    {
        parent::__construct();
    }
    
    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $person = new Personne();
        $person->setNom("nom")->setPrenom("prenom")->setDateNaissance(new DateTime("1998-03-02"))->setSexe("M");

        $person2 = new Personne();
        $person2->setNom("nom2")->setPrenom("prenom2")->setDateNaissance(new DateTime("1988-03-02"))->setSexe("F");

        $this->entityManager->persist($person);
        $this->entityManager->persist($person2);

        $typeRelation = new TypeRelation();
        $typeRelation->setNomRelation("Père");

        $this->entityManager->persist($typeRelation);

        $relation = new Relation();
        $relation->addPersonne($person)->addPersonne($person2)->setRelationType($typeRelation);

        $this->entityManager->persist($relation);

        $this->entityManager->flush();

       


        return 1;
    }
}