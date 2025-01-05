<?php

namespace App\Entity;

use App\Repository\SoldeCongeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SoldeCongeRepository::class)]
#[ORM\Table(name: 'solde_conge')]
class SoldeConge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $soldeConge = null;

    #[ORM\Column(nullable: true)]
    private ?int $soldeCongeEx = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\ManyToOne(inversedBy: 'soldeConges')]
    private ?Personnel $personnel = null;

    #[ORM\OneToMany(mappedBy: 'soldeconge', targetEntity: Conge::class)]
    private Collection $conges;

    public function __construct()
    {
        $this->conges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSoldeConge(): ?int
    {
        return $this->soldeConge;
    }

    public function setSoldeConge(?int $soldeConge): self
    {
        $this->soldeConge = $soldeConge;

        return $this;
    }

    public function getSoldeCongeEx(): ?int
    {
        return $this->soldeCongeEx;
    }

    public function setSoldeCongeEx(?int $soldeCongeEx): self
    {
        $this->soldeCongeEx = $soldeCongeEx;

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

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;

        return $this;
    }

    /**
     * @return Collection<int, Conge>
     */
    public function getConges(): Collection
    {
        return $this->conges;
    }

    public function addConge(Conge $conge): self
    {
        if (!$this->conges->contains($conge)) {
            $this->conges->add($conge);
            $conge->setSoldeconge($this);
        }

        return $this;
    }

    public function removeConge(Conge $conge): self
    {
        if ($this->conges->removeElement($conge)) {
            // set the owning side to null (unless already changed)
            if ($conge->getSoldeconge() === $this) {
                $conge->setSoldeconge(null);
            }
        }

        return $this;
    }
}
