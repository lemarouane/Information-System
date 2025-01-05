<?php

namespace App\Entity\Etudiant;

use App\Repository\EtuDiplomeCarteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtuDiplomeCarteRepository::class)]
#[ORM\Table(name: 'etudiplomecarte')] 

class EtuDiplomeCarte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDemande = null;

    #[ORM\Column(length: 25)]
    private ?string $type = null;

    #[ORM\Column(length: 4)]
    private ?string $anneeUniv = null;

    #[ORM\Column(length: 30)]
    private ?string $valueType = null;

    #[ORM\Column(length: 255)]
    private ?string $decision = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $filiere = null;

    #[ORM\ManyToOne(inversedBy: 'etuDiplomeCartes')]
    private ?Etudiants $codeEtudiant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $validateur = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $typeF = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->dateDemande;
    }

    public function setDateDemande(\DateTimeInterface $dateDemande): self
    {
        $this->dateDemande = $dateDemande;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAnneeUniv(): ?string
    {
        return $this->anneeUniv;
    }

    public function setAnneeUniv(string $anneeUniv): self
    {
        $this->anneeUniv = $anneeUniv;

        return $this;
    }

    public function getValueType(): ?string
    {
        return $this->valueType;
    }

    public function setValueType(string $valueType): self
    {
        $this->valueType = $valueType;

        return $this;
    }

    public function getDecision(): ?string
    {
        return $this->decision;
    }

    public function setDecision(string $decision): self
    {
        $this->decision = $decision;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getFiliere(): ?string
    {
        return $this->filiere;
    }

    public function setFiliere(?string $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }

    public function getCodeEtudiant(): ?Etudiants
    {
        return $this->codeEtudiant;
    }

    public function setCodeEtudiant(?Etudiants $codeEtudiant): self
    {
        $this->codeEtudiant = $codeEtudiant;

        return $this;
    }

    public function getDateValidation(): ?\DateTimeInterface
    {
        return $this->dateValidation;
    }

    public function setDateValidation(?\DateTimeInterface $dateValidation): self
    {
        $this->dateValidation = $dateValidation;

        return $this;
    }

    public function getValidateur(): ?string
    {
        return $this->validateur;
    }

    public function setValidateur(?string $validateur): self
    {
        $this->validateur = $validateur;

        return $this;
    }

    public function getTypeF(): ?string
    {
        return $this->typeF;
    }

    public function setTypeF(?string $typeF): self
    {
        $this->typeF = $typeF;

        return $this;
    }
}
