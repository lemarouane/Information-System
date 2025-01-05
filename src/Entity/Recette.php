<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 10)]
    private ?string $nature = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant = null;

    #[ORM\OneToMany(mappedBy: 'rubrique', targetEntity: RubriqueRecette::class)]
    private Collection $rubriqueRecettes;

    public function __construct()
    {
        $this->rubriqueRecettes = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(string $nature): self
    {
        $this->nature = $nature;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * @return Collection<int, RubriqueRecette>
     */
    public function getRubriqueRecettes(): Collection
    {
        return $this->rubriqueRecettes;
    }

    public function addRubriqueRecette(RubriqueRecette $rubriqueRecette): self
    {
        if (!$this->rubriqueRecettes->contains($rubriqueRecette)) {
            $this->rubriqueRecettes->add($rubriqueRecette);
            $rubriqueRecette->setRubrique($this);
        }

        return $this;
    }

    public function removeRubriqueRecette(RubriqueRecette $rubriqueRecette): self
    {
        if ($this->rubriqueRecettes->removeElement($rubriqueRecette)) {
            // set the owning side to null (unless already changed)
            if ($rubriqueRecette->getRubrique() === $this) {
                $rubriqueRecette->setRubrique(null);
            }
        }

        return $this;
    }

    
}
