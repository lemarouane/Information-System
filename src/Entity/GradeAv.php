<?php

namespace App\Entity;

use App\Repository\GradeAvRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GradeAvRepository::class)]
class GradeAv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $rapide = null;

    #[ORM\Column(nullable: true)]
    private ?int $exceptionnel = null;

    #[ORM\Column(nullable: true)]
    private ?int $normale = null;

    #[ORM\ManyToOne(inversedBy: 'gradeAvs')]
    private ?Grades $etatActuel = null;

    #[ORM\ManyToOne(inversedBy: 'gradesPro')]
    private ?Grades $etatPropose = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRapide(): ?int
    {
        return $this->rapide;
    }

    public function setRapide(?int $rapide): self
    {
        $this->rapide = $rapide;

        return $this;
    }

    public function getExceptionnel(): ?int
    {
        return $this->exceptionnel;
    }

    public function setExceptionnel(?int $exceptionnel): self
    {
        $this->exceptionnel = $exceptionnel;

        return $this;
    }

    public function getNormale(): ?int
    {
        return $this->normale;
    }

    public function setNormale(?int $normale): self
    {
        $this->normale = $normale;

        return $this;
    }

    public function getEtatActuel(): ?Grades
    {
        return $this->etatActuel;
    }

    public function setEtatActuel(?Grades $etatActuel): self
    {
        $this->etatActuel = $etatActuel;

        return $this;
    }

    public function getEtatPropose(): ?Grades
    {
        return $this->etatPropose;
    }

    public function setEtatPropose(?Grades $etatPropose): self
    {
        $this->etatPropose = $etatPropose;

        return $this;
    }
}
