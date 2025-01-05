<?php

namespace App\Entity;

use App\Repository\DechargeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DechargeRepository::class)]
class Decharge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $NumDecharge = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Exercice = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateDecharge = null;

    #[ORM\ManyToOne(inversedBy: 'decharges')]
    private ?Personnel $personnel = null;

    #[ORM\ManyToMany(targetEntity: RegistreInventaire::class, inversedBy: 'decharges')]
    private Collection $inventaire;

    #[ORM\OneToMany(mappedBy: 'Decharge', targetEntity: Affectation::class, orphanRemoval: true, cascade:["persist","remove"])]
    private Collection $affectations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $local = null;

    public function __construct()
    {
        $this->inventaire = new ArrayCollection();
        $this->affectations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNumDecharge(): ?string
    {
        return $this->NumDecharge;
    }

    public function setNumDecharge(string $NumDecharge): self
    {
        $this->NumDecharge = $NumDecharge;

        return $this;
    }

    public function getExercice(): ?string
    {
        return $this->Exercice;
    }

    public function setExercice(?string $Exercice): self
    {
        $this->Exercice = $Exercice;

        return $this;
    }

    public function getDateDecharge(): ?\DateTimeInterface
    {
        return $this->DateDecharge;
    }

    public function setDateDecharge(?\DateTimeInterface $DateDecharge): self
    {
        $this->DateDecharge = $DateDecharge;

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
     * @return Collection<int, RegistreInventaire>
     */
    public function getInventaire(): Collection
    {
        return $this->inventaire;
    }

    public function addInventaire(RegistreInventaire $inventaire): self
    {
        if (!$this->inventaire->contains($inventaire)) {
            $this->inventaire->add($inventaire);
        }

        return $this;
    }

    public function removeInventaire(RegistreInventaire $inventaire): self
    {
        $this->inventaire->removeElement($inventaire);

        return $this;
    }

    /**
     * @return Collection<int, Affectation>
     */
    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(Affectation $affectation): self
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations->add($affectation);
            $affectation->setDecharge($this);
        }

        return $this;
    }

    public function removeAffectation(Affectation $affectation): self
    {
        if ($this->affectations->removeElement($affectation)) {
            // set the owning side to null (unless already changed)
            if ($affectation->getDecharge() === $this) {
                $affectation->setDecharge(null);
            }
        }

        return $this;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(?string $local): self
    {
        $this->local = $local;

        return $this;
    }
}
