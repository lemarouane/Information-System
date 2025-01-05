<?php

namespace App\Entity;

use App\Repository\BudgetEntreeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetEntreeRepository::class)]
class BudgetEntree
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

    #[ORM\ManyToOne(inversedBy: 'budgetEntrees')]
    private ?Budget $budget = null;

    #[ORM\ManyToOne(inversedBy: 'budgetEntrees')]
    private ?RubriqueRecette $rubrique_recette = null;

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

    public function getRubriqueRecette(): ?RubriqueRecette
    {
        return $this->rubrique_recette;
    }

    public function setRubriqueRecette(?RubriqueRecette $rubrique_recette): self
    {
        $this->rubrique_recette = $rubrique_recette;

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
