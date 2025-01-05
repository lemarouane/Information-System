<?php

namespace App\Entity;

use App\Repository\EngagementheureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EngagementheureRepository::class)]
class Engagementheure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $jours = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureDebut = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heureFin = null;

    #[ORM\ManyToOne(inversedBy: 'engagements')]
    private ?Ficheheure $ficheheure = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJours(): ?string
    {
        return $this->jours;
    }

    public function setJours(?string $jours): self
    {
        $this->jours = $jours;

        return $this;
    }

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(?\DateTimeInterface $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(?\DateTimeInterface $heureFin): self
    {
        $this->heureFin = $heureFin;

        return $this;
    }

    public function getFicheheure(): ?Ficheheure
    {
        return $this->ficheheure;
    }

    public function setFicheheure(?Ficheheure $ficheheure): self
    {
        $this->ficheheure = $ficheheure;

        return $this;
    }
}
