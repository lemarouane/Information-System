<?php

namespace App\Entity;

use App\Repository\DiplomeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiplomeRepository::class)]
class Diplome
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $codeDiplome = null;

    #[ORM\Column(length: 255)]
    private ?string $designationFR = null;

    #[ORM\Column(length: 255)]
    private ?string $designationAR = null;

    #[ORM\OneToMany(mappedBy: 'diplomeId', targetEntity: Personnel::class)]
    private Collection $personnels;

    public function __construct()
    {
        $this->personnels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeDiplome(): ?string
    {
        return $this->codeDiplome;
    }

    public function setCodeDiplome(string $codeDiplome): self
    {
        $this->codeDiplome = $codeDiplome;

        return $this;
    }

    public function getDesignationFR(): ?string
    {
        return $this->designationFR;
    }

    public function setDesignationFR(string $designationFR): self
    {
        $this->designationFR = $designationFR;

        return $this;
    }

    public function getDesignationAR(): ?string
    {
        return $this->designationAR;
    }

    public function setDesignationAR(string $designationAR): self
    {
        $this->designationAR = $designationAR;

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
            $personnel->setDiplomeId($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getDiplomeId() === $this) {
                $personnel->setDiplomeId(null);
            }
        }

        return $this;
    }
}
