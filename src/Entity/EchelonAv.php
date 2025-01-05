<?php

namespace App\Entity;

use App\Repository\EchelonAvRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EchelonAvRepository::class)]
class EchelonAv
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

    #[ORM\ManyToOne(inversedBy: 'echelonAvs')]
    private ?Echelon $etatActuel = null;

    #[ORM\ManyToOne(inversedBy: 'echelonPro')]
    private ?Echelon $etatPropose = null;

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

    public function getEtatActuel(): ?Echelon
    {
        return $this->etatActuel;
    }

    public function setEtatActuel(?Echelon $etatActuel): self
    {
        $this->etatActuel = $etatActuel;

        return $this;
    }

    public function getEtatPropose(): ?Echelon
    {
        return $this->etatPropose;
    }

    public function setEtatPropose(?Echelon $etatPropose): self
    {
        $this->etatPropose = $etatPropose;

        return $this;
    }
}
