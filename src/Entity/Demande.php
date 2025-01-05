<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]
class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NumDemande = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    private ?Personnel $personnel = null;

    #[ORM\OneToMany(mappedBy: 'demande', targetEntity: DemandeLigne::class, orphanRemoval: true, cascade:["persist","remove"])]
    private Collection $demandeLignes;

    public function __construct()
    {
        $this->demandeLignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumDemande(): ?string
    {
        return $this->NumDemande;
    }

    public function setNumDemande(?string $NumDemande): self
    {
        $this->NumDemande = $NumDemande;

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
     * @return Collection<int, DemandeLigne>
     */
    public function getDemandeLignes(): Collection
    {
        return $this->demandeLignes;
    }

    public function addDemandeLigne(DemandeLigne $demandeLigne): self
    {
        if (!$this->demandeLignes->contains($demandeLigne)) {
            $this->demandeLignes->add($demandeLigne);
            $demandeLigne->setDemande($this);
        }

        return $this;
    }

    public function removeDemandeLigne(DemandeLigne $demandeLigne): self
    {
        if ($this->demandeLignes->removeElement($demandeLigne)) {
            // set the owning side to null (unless already changed)
            if ($demandeLigne->getDemande() === $this) {
                $demandeLigne->setDemande(null);
            }
        }

        return $this;
    }
}
