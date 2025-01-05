<?php

namespace App\Entity;

use App\Repository\StageencadRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StageencadRepository::class)]
class Stageencad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $stage = null;

    #[ORM\Column(nullable: true)]
    private ?int $encadrant = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneeuniv = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStage(): ?int
    {
        return $this->stage;
    }

    public function setStage(?int $stage): self
    {
        $this->stage = $stage;

        return $this;
    }

    public function getEncadrant(): ?int
    {
        return $this->encadrant;
    }

    public function setEncadrant(?int $encadrant): self
    {
        $this->encadrant = $encadrant;

        return $this;
    }

    public function getAnneeuniv(): ?int
    {
        return $this->anneeuniv;
    }

    public function setAnneeuniv(?int $anneeuniv): self
    {
        $this->anneeuniv = $anneeuniv;

        return $this;
    }
}
