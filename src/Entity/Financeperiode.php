<?php

namespace App\Entity;

use App\Repository\FinanceperiodeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FinanceperiodeRepository::class)]
class Financeperiode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $periode = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $actif = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(?string $periode): self
    {
        $this->periode = $periode;

        return $this;
    }

    public function getActif(): ?string
    {
        return $this->actif;
    }

    public function setActif(?string $actif): self
    {
        $this->actif = $actif;

        return $this;
    }
}
