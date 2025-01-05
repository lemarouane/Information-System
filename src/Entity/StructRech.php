<?php

namespace App\Entity;

use App\Repository\StructRechRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StructRechRepository::class)]
class StructRech
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelleStructure = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $abrevStructure = null;

    #[ORM\ManyToOne(inversedBy: 'structReches')]
    private ?TypeStructRech $typeStructure = null;

    #[ORM\OneToMany(mappedBy: 'structureRech', targetEntity: Personnel::class)]
    private Collection $personnels;

    #[ORM\Column(nullable: true)]
    private ?int $codes = null;

    public function __construct()
    {
        $this->personnels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleStructure(): ?string
    {
        return $this->libelleStructure;
    }

    public function setLibelleStructure(?string $libelleStructure): self
    {
        $this->libelleStructure = $libelleStructure;

        return $this;
    }

    public function getAbrevStructure(): ?string
    {
        return $this->abrevStructure;
    }

    public function setAbrevStructure(?string $abrevStructure): self
    {
        $this->abrevStructure = $abrevStructure;

        return $this;
    }

    public function getTypeStructure(): ?TypeStructRech
    {
        return $this->typeStructure;
    }

    public function setTypeStructure(?TypeStructRech $typeStructure): self
    {
        $this->typeStructure = $typeStructure;

        return $this;
    }

    /**
     * @return Collection<int, Personnel>
     */
    public function getPersonnels(): Collection
    {
        return $this->personnels;
    }

    public function addPersonnel(Personnel $personnel): self
    {
        if (!$this->personnels->contains($personnel)) {
            $this->personnels->add($personnel);
            $personnel->setStructureRech($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getStructureRech() === $this) {
                $personnel->setStructureRech(null);
            }
        }

        return $this;
    }

    public function getCodes(): ?int
    {
        return $this->codes;
    }

    public function setCodes(?int $codes): self
    {
        $this->codes = $codes;

        return $this;
    }
}
