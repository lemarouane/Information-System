<?php

namespace App\Entity;

use App\Repository\ProgrammeElementBudgetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeElementBudgetRepository::class)]
class ProgrammeElementBudget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'elementBudget')]
    private ?Rubrique $rubrique = null;

    #[ORM\ManyToOne(inversedBy: 'element')]
    private ?ProgrammeEmploiBudget $programme = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRubrique(): ?Rubrique
    {
        return $this->rubrique;
    }

    public function setRubrique(?Rubrique $rubrique): self
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    public function getProgramme(): ?ProgrammeEmploiBudget
    {
        return $this->programme;
    }

    public function setProgramme(?ProgrammeEmploiBudget $programme): self
    {
        $this->programme = $programme;

        return $this;
    }
}
