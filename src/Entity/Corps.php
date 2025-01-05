<?php

namespace App\Entity;

use App\Repository\CorpsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CorpsRepository::class)]
class Corps
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $designationFR = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $designationAR = null;

    #[ORM\OneToMany(mappedBy: 'corpsId', targetEntity: Grades::class)]
    private Collection $grades;

    #[ORM\OneToMany(mappedBy: 'corpsId', targetEntity: Personnel::class)]
    private Collection $personnels;

    #[ORM\OneToMany(mappedBy: 'corps', targetEntity: Avancement::class)]
    private Collection $avancements;

    public function __construct()
    {
        $this->grades = new ArrayCollection();
        $this->personnels = new ArrayCollection();
        $this->avancements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignationFR(): ?string
    {
        return $this->designationFR;
    }

    public function setDesignationFR(?string $designationFR): self
    {
        $this->designationFR = $designationFR;

        return $this;
    }

    public function getDesignationAR(): ?string
    {
        return $this->designationAR;
    }

    public function setDesignationAR(?string $designationAR): self
    {
        $this->designationAR = $designationAR;

        return $this;
    }

    /**
     * @return Collection<int, Grades>
     */
    public function getGrades(): Collection
    {
        return $this->grades;
    }

    public function addGrade(Grades $grade): self
    {
        if (!$this->grades->contains($grade)) {
            $this->grades->add($grade);
            $grade->setCorpsId($this);
        }

        return $this;
    }

    public function removeGrade(Grades $grade): self
    {
        if ($this->grades->removeElement($grade)) {
            // set the owning side to null (unless already changed)
            if ($grade->getCorpsId() === $this) {
                $grade->setCorpsId(null);
            }
        }

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
            $personnel->setCorpsId($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getCorpsId() === $this) {
                $personnel->setCorpsId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Avancement>
     */
    public function getAvancements(): Collection
    {
        return $this->avancements;
    }

    public function addAvancement(Avancement $avancement): self
    {
        if (!$this->avancements->contains($avancement)) {
            $this->avancements->add($avancement);
            $avancement->setCorps($this);
        }

        return $this;
    }

    public function removeAvancement(Avancement $avancement): self
    {
        if ($this->avancements->removeElement($avancement)) {
            // set the owning side to null (unless already changed)
            if ($avancement->getCorps() === $this) {
                $avancement->setCorps(null);
            }
        }

        return $this;
    }
}
