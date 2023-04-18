<?php

namespace App\Entity;

use App\Repository\TypeRelationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRelationRepository::class)]
class TypeRelation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom_relation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomRelation(): ?string
    {
        return $this->nom_relation;
    }

    public function setNomRelation(string $nom_relation): self
    {
        $this->nom_relation = $nom_relation;

        return $this;
    }
}
