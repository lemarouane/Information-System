<?php

namespace App\Entity;

use App\Repository\CycleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CycleRepository::class)]
class Cycle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomCycle = null;

    #[ORM\OneToMany(mappedBy: 'cycle', targetEntity: Filiere::class)]
    private Collection $filieres;

    public function __construct()
    {
        $this->filieres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCycle(): ?string
    {
        return $this->nomCycle;
    }

    public function setNomCycle(?string $nomCycle): self
    {
        $this->nomCycle = $nomCycle;

        return $this;
    }

    /**
     * @return Collection<int, Filiere>
     */
    public function getFilieres(): Collection
    {
        return $this->filieres;
    }

    public function addFiliere(Filiere $filiere): self
    {
        if (!$this->filieres->contains($filiere)) {
            $this->filieres->add($filiere);
            $filiere->setCycle($this);
        }

        return $this;
    }

    public function removeFiliere(Filiere $filiere): self
    {
        if ($this->filieres->removeElement($filiere)) {
            // set the owning side to null (unless already changed)
            if ($filiere->getCycle() === $this) {
                $filiere->setCycle(null);
            }
        }

        return $this;
    }
}
