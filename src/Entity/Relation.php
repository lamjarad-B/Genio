<?php

namespace App\Entity;

use App\Repository\RelationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RelationRepository::class)]
class Relation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Personne::class, inversedBy: 'relations')]
    private Collection $personnes;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeRelation $relation_type = null;

    public function __construct()
    {
        $this->personnes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Personne>
     */
    public function getPersonnes(): Collection
    {
        return $this->personnes;
    }

    public function addPersonne(Personne $personne): self
    {
        if (!$this->personnes->contains($personne)) {
            $this->personnes->add($personne);
        }

        return $this;
    }

    public function removePersonne(Personne $personne): self
    {
        $this->personnes->removeElement($personne);

        return $this;
    }

    public function getRelationType(): ?TypeRelation
    {
        return $this->relation_type;
    }

    public function setRelationType(TypeRelation $relation_type): self
    {
        $this->relation_type = $relation_type;

        return $this;
    }
}
