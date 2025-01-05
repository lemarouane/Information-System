<?php

namespace App\Entity;

use App\Repository\EchelonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EchelonRepository::class)]
class Echelon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $designation = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbAnnee = null;

    #[ORM\ManyToOne(inversedBy: 'echelons')]
    private ?Grades $grade = null;

    #[ORM\OneToMany(mappedBy: 'echelonId', targetEntity: Personnel::class)]
    private Collection $personnels;

    #[ORM\OneToMany(mappedBy: 'echelon', targetEntity: Avancement::class)]
    #[ORM\OrderBy(["dateDeci" => "ASC"])]
    private Collection $avancements;

    #[ORM\OneToMany(mappedBy: 'etatActuel', targetEntity: EchelonAv::class)]
    private Collection $echelonAvs;

    #[ORM\OneToMany(mappedBy: 'etatPropose', targetEntity: EchelonAv::class)]
    private Collection $echelonPro;

    public function __construct()
    {
        $this->personnels = new ArrayCollection();
        $this->avancements = new ArrayCollection();
        $this->echelonAvs = new ArrayCollection();
        $this->echelonPro = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getNbAnnee(): ?int
    {
        return $this->nbAnnee;
    }

    public function setNbAnnee(?int $nbAnnee): self
    {
        $this->nbAnnee = $nbAnnee;

        return $this;
    }

    public function getGrade(): ?Grades
    {
        return $this->grade;
    }

    public function setGrade(?Grades $grade): self
    {
        $this->grade = $grade;

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
            $personnel->setEchelonId($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getEchelonId() === $this) {
                $personnel->setEchelonId(null);
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
            $avancement->setEchelon($this);
        }

        return $this;
    }

    public function removeAvancement(Avancement $avancement): self
    {
        if ($this->avancements->removeElement($avancement)) {
            // set the owning side to null (unless already changed)
            if ($avancement->getEchelon() === $this) {
                $avancement->setEchelon(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EchelonAv>
     */
    public function getEchelonAvs(): Collection
    {
        return $this->echelonAvs;
    }

    public function addEchelonAv(EchelonAv $echelonAv): self
    {
        if (!$this->echelonAvs->contains($echelonAv)) {
            $this->echelonAvs->add($echelonAv);
            $echelonAv->setEtatActuel($this);
        }

        return $this;
    }

    public function removeEchelonAv(EchelonAv $echelonAv): self
    {
        if ($this->echelonAvs->removeElement($echelonAv)) {
            // set the owning side to null (unless already changed)
            if ($echelonAv->getEtatActuel() === $this) {
                $echelonAv->setEtatActuel(null);
            }
        }

        return $this;
    }

     /**
     * @return Collection<int, EchelonAv>
     */
    public function getEchelonPro(): Collection
    {
        return $this->echelonPro;
    }

    public function addEchelonPro(EchelonAv $echelonPro): self
    {
        if (!$this->echelonPro->contains($echelonPro)) {
            $this->echelonPro->add($echelonPro);
            $echelonPro->setEtatPropose($this);
        }

        return $this;
    }

    public function removeEchelonPro(EchelonAv $echelonPro): self
    {
        if ($this->echelonPro->removeElement($echelonPro)) {
            // set the owning side to null (unless already changed)
            if ($echelonPro->getEtatPropose() === $this) {
                $echelonPro->setEtatPropose(null);
            }
        }

        return $this;
    }
}
