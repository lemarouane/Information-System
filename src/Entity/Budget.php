<?php

namespace App\Entity;

use App\Repository\BudgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
class Budget
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

    #[ORM\OneToMany(mappedBy: 'budget', targetEntity: BudgetEntree::class , cascade:["persist","remove"])]
    private Collection $budgetEntrees;

    #[ORM\OneToMany(mappedBy: 'budget', targetEntity: BudgetSortie::class  , cascade:["persist","remove"])]
    private Collection $budgetSorties;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $totale_entree = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $totale_sortie = null;



 

    public function __construct()
    {
        $this->budgetEntrees = new ArrayCollection();
        $this->budgetSorties = new ArrayCollection();
    }

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
 
    /**
     * @return Collection<int, BudgetEntree>
     */
    public function getBudgetEntrees(): Collection
    {
        return $this->budgetEntrees;
    }

    public function addBudgetEntree(BudgetEntree $budgetEntree): self
    {
        if (!$this->budgetEntrees->contains($budgetEntree)) {
            $this->budgetEntrees->add($budgetEntree);
            $budgetEntree->setBudget($this);
        }

        return $this;
    }

    public function removeBudgetEntree(BudgetEntree $budgetEntree): self
    {
        if ($this->budgetEntrees->removeElement($budgetEntree)) {
            // set the owning side to null (unless already changed)
            if ($budgetEntree->getBudget() === $this) {
                $budgetEntree->setBudget(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BudgetSortie>
     */
    public function getBudgetSorties(): Collection
    {
        return $this->budgetSorties;
    }

    public function addBudgetSorty(BudgetSortie $budgetSorty): self
    {
        if (!$this->budgetSorties->contains($budgetSorty)) {
            $this->budgetSorties->add($budgetSorty);
            $budgetSorty->setBudget($this);
        }

        return $this;
    }

    public function removeBudgetSorty(BudgetSortie $budgetSorty): self
    {
        if ($this->budgetSorties->removeElement($budgetSorty)) {
            // set the owning side to null (unless already changed)
            if ($budgetSorty->getBudget() === $this) {
                $budgetSorty->setBudget(null);
            }
        }

        return $this;
    }

    public function getTotaleEntree(): ?string
    {
        return $this->totale_entree;
    }

    public function setTotaleEntree(?string $totale_entree): self
    {
        $this->totale_entree = $totale_entree;

        return $this;
    }

    public function getTotaleSortie(): ?string
    {
        return $this->totale_sortie;
    }

    public function setTotaleSortie(?string $totale_sortie): self
    {
        $this->totale_sortie = $totale_sortie;

        return $this;
    }

 

 

 
}
