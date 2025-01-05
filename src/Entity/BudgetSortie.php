<?php

namespace App\Entity;

use App\Repository\BudgetSortieRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetSortieRepository::class)]
class BudgetSortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $montant = null;

    #[ORM\ManyToOne(inversedBy: 'budgetSorties')]
    private ?Budget $budget = null;

    #[ORM\Column(nullable: true)]
    private ?int $type_structure = null;
 
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $structure = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $temoin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getBudget(): ?Budget
    {
        return $this->budget;
    }

    public function setBudget(?Budget $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getTypeStructure(): ?int
    {
        return $this->type_structure;
    }

    public function setTypeStructure(?int $type_structure): self
    {
        $this->type_structure = $type_structure;

        return $this;
    }

    public function getStructure(): ?string
    {
        return $this->structure;
    }

    public function setStructure(?string $structure): self
    {
        $this->structure = $structure;

        return $this;
    }

    public function getTemoin(): ?string
    {
        return $this->temoin;
    }

    public function setTemoin(?string $temoin): self
    {
        $this->temoin = $temoin;

        return $this;
    }
}
