<?php

namespace App\Entity;

use App\Repository\FiliereRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiliereRepository::class)]
class Filiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomFiliere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeEtab = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeApo = null;

    #[ORM\ManyToOne(inversedBy: 'filieres')]
    private ?Cycle $cycle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFiliere(): ?string
    {
        return $this->nomFiliere;
    }

    public function setNomFiliere(?string $nomFiliere): self
    {
        $this->nomFiliere = $nomFiliere;

        return $this;
    }

    public function getCodeEtab(): ?string
    {
        return $this->codeEtab;
    }

    public function setCodeEtab(?string $codeEtab): self
    {
        $this->codeEtab = $codeEtab;

        return $this;
    }

    public function getCodeApo(): ?string
    {
        return $this->codeApo;
    }

    public function setCodeApo(?string $codeApo): self
    {
        $this->codeApo = $codeApo;

        return $this;
    }

    public function getCycle(): ?Cycle
    {
        return $this->cycle;
    }

    public function setCycle(?Cycle $cycle): self
    {
        $this->cycle = $cycle;

        return $this;
    }
}
