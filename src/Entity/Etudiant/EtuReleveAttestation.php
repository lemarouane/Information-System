<?php

namespace App\Entity\Etudiant;

use App\Repository\EtuReleveAttestationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtuReleveAttestationRepository::class)]
#[ORM\Table(name: 'etureleveattestation')] 
class EtuReleveAttestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDemande = null;

    #[ORM\Column(length: 20)]
    private ?string $type = null;

    #[ORM\Column(length: 4)]
    private ?string $anneeUniv = null;

    #[ORM\Column(length: 4)]
    private ?string $anneeEtape = null;

    #[ORM\Column(length: 10)]
    private ?string $codeEtape = null;

    #[ORM\Column(length: 255)]
    private ?string $decision = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\ManyToOne(inversedBy: 'etuReleveAttestations')]
    private ?Etudiants $codeEtudiant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $validateur = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $typeF = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $version = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $version1 = null;
    
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

    public function getAnneeEtape(): ?string
    {
        return $this->anneeEtape;
    }

    public function setAnneeEtape(string $anneeEtape): self
    {
        $this->anneeEtape = $anneeEtape;

        return $this;
    }

    public function getCodeEtape(): ?string
    {
        return $this->codeEtape;
    }

    public function setCodeEtape(string $codeEtape): self
    {
        $this->codeEtape = $codeEtape;

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

    public function getVersion(): ?array
    {
        return $this->version;
    }

    public function setVersion(?array $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion1(): ?string
    {
        return $this->version1;
    }

    public function setVersion1(?string $version1): self
    {
        $this->version1 = $version1;

        return $this;
    }
}
