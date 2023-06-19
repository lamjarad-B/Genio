<?php

namespace App\Entity;

use App\Repository\TypeRelationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'relation_type', targetEntity: Relation::class, orphanRemoval: true)]
    private Collection $relations;

    public function __construct()
    {
        $this->relations = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Relation>
     */
    public function getRelations(): Collection
    {
        return $this->relations;
    }

    public function addRelation(Relation $relation): self
    {
        if (!$this->relations->contains($relation)) {
            $this->relations->add($relation);
            $relation->setRelationType($this);
        }

        return $this;
    }

    public function removeRelation(Relation $relation): self
    {
        if ($this->relations->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getRelationType() === $this) {
                $relation->setRelationType(null);
            }
        }

        return $this;
    }

    public function __toString(){ 
        return $this->getNomRelation();
    }
}
