<?php

namespace App\Entity;

use App\Repository\CongeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CongeRepository::class)]
class Conge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'conges')]
    private ?SoldeConge $soldeconge = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateReprise = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $typeConge = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbJour = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnvoie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifs = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $niveau = null;

    #[ORM\Column(nullable: true)]
    private ?bool $bloque = null;

    #[ORM\ManyToOne(inversedBy: 'conges')]
    private ?Personnel $personnel = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motifRefu = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSoldeconge(): ?SoldeConge
    {
        return $this->soldeconge;
    }

    public function setSoldeconge(?SoldeConge $soldeconge): self
    {
        $this->soldeconge = $soldeconge;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateReprise(): ?\DateTimeInterface
    {
        return $this->dateReprise;
    }

    public function setDateReprise(?\DateTimeInterface $dateReprise): self
    {
        $this->dateReprise = $dateReprise;

        return $this;
    }

    public function getTypeConge(): ?string
    {
        return $this->typeConge;
    }

    public function setTypeConge(?string $typeConge): self
    {
        $this->typeConge = $typeConge;

        return $this;
    }

    public function getNbJour(): ?int
    {
        return $this->nbJour;
    }

    public function setNbJour(?int $nbJour): self
    {
        $this->nbJour = $nbJour;

        return $this;
    }

    public function getDateEnvoie(): ?\DateTimeInterface
    {
        return $this->dateEnvoie;
    }

    public function setDateEnvoie(?\DateTimeInterface $dateEnvoie): self
    {
        $this->dateEnvoie = $dateEnvoie;

        return $this;
    }

    public function getMotifs(): ?string
    {
        return $this->motifs;
    }

    public function setMotifs(?string $motifs): self
    {
        $this->motifs = $motifs;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(?string $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function isBloque(): ?bool
    {
        return $this->bloque;
    }

    public function setBloque(?bool $bloque): self
    {
        $this->bloque = $bloque;

        return $this;
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

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getMotifRefu(): ?string
    {
        return $this->motifRefu;
    }

    public function setMotifRefu(?string $motifRefu): self
    {
        $this->motifRefu = $motifRefu;

        return $this;
    }
}
