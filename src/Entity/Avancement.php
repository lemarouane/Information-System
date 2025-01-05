<?php

namespace App\Entity;

use App\Repository\AvancementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvancementRepository::class)]
class Avancement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'avancements')]
    private ?Personnel $personnel = null;

    #[ORM\ManyToOne(inversedBy: 'avancements')]
    private ?Grades $grade = null;

    #[ORM\ManyToOne(inversedBy: 'avancements')]
    private ?Corps $corps = null;

    #[ORM\ManyToOne(inversedBy: 'avancements')]
    private ?Echelon $echelon = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDeci = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateGrade = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $arrete = null;

    #[ORM\Column(nullable: true)]
    private ?int $numDeci = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGrade(): ?Grades
    {
        return $this->grade;
    }

    public function setGrade(?Grades $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getCorps(): ?Corps
    {
        return $this->corps;
    }

    public function setCorps(?Corps $corps): self
    {
        $this->corps = $corps;

        return $this;
    }

    public function getEchelon(): ?Echelon
    {
        return $this->echelon;
    }

    public function setEchelon(?Echelon $echelon): self
    {
        $this->echelon = $echelon;

        return $this;
    }

    public function getDateDeci(): ?\DateTimeInterface
    {
        return $this->dateDeci;
    }

    public function setDateDeci(?\DateTimeInterface $dateDeci): self
    {
        $this->dateDeci = $dateDeci;

        return $this;
    }

    public function getDateGrade(): ?\DateTimeInterface
    {
        return $this->dateGrade;
    }

    public function setDateGrade(?\DateTimeInterface $dateGrade): self
    {
        $this->dateGrade = $dateGrade;

        return $this;
    }

    public function getArrete(): ?string
    {
        return $this->arrete;
    }

    public function setArrete(?string $arrete): self
    {
        $this->arrete = $arrete;

        return $this;
    }

    public function getNumDeci(): ?int
    {
        return $this->numDeci;
    }

    public function setNumDeci(?int $numDeci): self
    {
        $this->numDeci = $numDeci;

        return $this;
    }
}
