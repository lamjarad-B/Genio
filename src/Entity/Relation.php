<?php

namespace App\Entity;

use App\Repository\RelationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RelationRepository::class)]
class Relation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'relations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $personne1 = null;

    #[ORM\ManyToOne(inversedBy: 'relations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeRelation $relation_type = null;

    #[ORM\ManyToOne(inversedBy: 'relations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personne $personne2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonne1(): ?Personne
    {
        return $this->personne1;
    }

    public function setPersonne1(?Personne $personne1): self
    {
        $this->personne1 = $personne1;

        return $this;
    }

    public function getPersonne2(): ?Personne
    {
        return $this->personne2;
    }

    public function setPersonne2(?Personne $personne2): self
    {
        $this->personne2 = $personne2;

        return $this;
    }

    public function getRelationType(): ?TypeRelation
    {
        return $this->relation_type;
    }

    public function setRelationType(?TypeRelation $relation_type): self
    {
        $this->relation_type = $relation_type;

        return $this;
    }
}
