<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonneRepository::class)]
class Personne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_naissance = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_deces = null;

    #[ORM\Column(length: 1)]
    private ?string $sexe = null;

    #[ORM\OneToMany(mappedBy: 'personne1', targetEntity: Relation::class, orphanRemoval: true)]
    private Collection $relationsPersonne1;

    #[ORM\OneToMany(mappedBy: 'personne2', targetEntity: Relation::class, orphanRemoval: true)]
    private Collection $relationsPersonne2;

    public function __construct()
    {
        $this->relationsPersonne1 = new ArrayCollection();
        $this->relationsPersonne2 = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getDateDeces(): ?\DateTimeInterface
    {
        return $this->date_deces;
    }

    public function setDateDeces(?\DateTimeInterface $date_deces): self
    {
        $this->date_deces = $date_deces;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    /**
     * @return Collection<int, Relation>
     */
    public function getRelationsPersonne1(): Collection
    {
        return $this->relationsPersonne1;
    }

    public function addRelationPersonne1(Relation $relation): self
    {
        if (!$this->relationsPersonne1->contains($relation)) {
            $this->relationsPersonne1->add($relation);
            $relation->setPersonne1($this);
        }

        return $this;
    }

    public function removeRelationPersonne1(Relation $relation): self
    {
        if ($this->relationsPersonne1->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getPersonne1() === $this) {
                $relation->setPersonne1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Relation>
     */
    public function getRelationsPersonne2(): Collection
    {
        return $this->relationsPersonne2;
    }

    public function addRelationPersonne2(Relation $relation): self
    {
        if (!$this->relationsPersonne2->contains($relation)) {
            $this->relationsPersonne2->add($relation);
            $relation->setPersonne2($this);
        }

        return $this;
    }

    public function removeRelationPersonne2(Relation $relation): self
    {
        if ($this->relationsPersonne2->removeElement($relation)) {
            // set the owning side to null (unless already changed)
            if ($relation->getPersonne2() === $this) {
                $relation->setPersonne2(null);
            }
        }

        return $this;
    }
}
