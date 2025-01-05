<?php

namespace App\Entity;

use App\Repository\GradesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GradesRepository::class)]
class Grades
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $designationFR = null;

    #[ORM\Column(length: 255)]
    private ?string $designationAR = null;

    #[ORM\ManyToOne(inversedBy: 'grades')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Corps $corpsId = null;

    #[ORM\OneToMany(mappedBy: 'grade', targetEntity: Echelon::class)]
    private Collection $echelons;

    #[ORM\OneToMany(mappedBy: 'gradeId', targetEntity: Personnel::class)]
    private Collection $personnels;

    #[ORM\OneToMany(mappedBy: 'grade', targetEntity: Avancement::class)]
    private Collection $avancements;

    #[ORM\OneToMany(mappedBy: 'etatActuel', targetEntity: GradeAv::class)]
    private Collection $gradeAvs;

    #[ORM\OneToMany(mappedBy: 'etatPropose', targetEntity: GradeAv::class)]
    private Collection $gradesPro;

    public function __construct()
    {
        $this->echelons = new ArrayCollection();
        $this->personnels = new ArrayCollection();
        $this->avancements = new ArrayCollection();
        $this->gradeAvs = new ArrayCollection();
        $this->gradesPro = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCorpsId(): ?Corps
    {
        return $this->corpsId;
    }

    public function setCorpsId(?Corps $corpsId): self
    {
        $this->corpsId = $corpsId;

        return $this;
    }

    /**
     * @return Collection<int, Echelon>
     */
    public function getEchelons(): Collection
    {
        return $this->echelons;
    }

    public function addEchelon(Echelon $echelon): self
    {
        if (!$this->echelons->contains($echelon)) {
            $this->echelons->add($echelon);
            $echelon->setGrade($this);
        }

        return $this;
    }

    public function removeEchelon(Echelon $echelon): self
    {
        if ($this->echelons->removeElement($echelon)) {
            // set the owning side to null (unless already changed)
            if ($echelon->getGrade() === $this) {
                $echelon->setGrade(null);
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
            $personnel->setGradeId($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getGradeId() === $this) {
                $personnel->setGradeId(null);
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
            $avancement->setGrade($this);
        }

        return $this;
    }

    public function removeAvancement(Avancement $avancement): self
    {
        if ($this->avancements->removeElement($avancement)) {
            // set the owning side to null (unless already changed)
            if ($avancement->getGrade() === $this) {
                $avancement->setGrade(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GradeAv>
     */
    public function getGradeAvs(): Collection
    {
        return $this->gradeAvs;
    }

    public function addGradeAv(GradeAv $gradeAv): self
    {
        if (!$this->gradeAvs->contains($gradeAv)) {
            $this->gradeAvs->add($gradeAv);
            $gradeAv->setEtatActuel($this);
        }

        return $this;
    }

    public function removeGradeAv(GradeAv $gradeAv): self
    {
        if ($this->gradeAvs->removeElement($gradeAv)) {
            // set the owning side to null (unless already changed)
            if ($gradeAv->getEtatActuel() === $this) {
                $gradeAv->setEtatActuel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GradeAv>
     */
    public function getGradesPro(): Collection
    {
        return $this->gradesPro;
    }

    public function addGradesPro(GradeAv $gradesPro): self
    {
        if (!$this->gradesPro->contains($gradesPro)) {
            $this->gradesPro->add($gradesPro);
            $gradesPro->setEtatPropose($this);
        }

        return $this;
    }

    public function removeGradesPro(GradeAv $gradesPro): self
    {
        if ($this->gradesPro->removeElement($gradesPro)) {
            // set the owning side to null (unless already changed)
            if ($gradesPro->getEtatPropose() === $this) {
                $gradesPro->setEtatPropose(null);
            }
        }

        return $this;
    }
}
