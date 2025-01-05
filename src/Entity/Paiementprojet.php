<?php

namespace App\Entity;

use App\Repository\PaiementprojetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementprojetRepository::class)] 
class Paiementprojet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'paiementprojets')]
    private ?Personnel $responsable = null;

    #[ORM\ManyToOne(inversedBy: 'paiementprojets')]
    private ?RubriqueRecette $rubrique = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datePaiement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOperation = null;

    #[ORM\Column(nullable: true)]
    private ?int $numOperation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $modePaiement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 25, scale: 2, nullable: true)]
    private ?string $montant = null;

    #[ORM\Column(nullable: true)]
    private ?int $numRP = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $intitule = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emetteur = null;

    public function getId(): ?int
    { 
        return $this->id;
    }

    public function getResponsable(): ?Personnel
    {
        return $this->responsable;
    }

    public function setResponsable(?Personnel $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function getRubrique(): ?RubriqueRecette
    {
        return $this->rubrique;
    }

    public function setRubrique(?RubriqueRecette $rubrique): self
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(?\DateTimeInterface $datePaiement): self
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getDateOperation(): ?\DateTimeInterface
    {
        return $this->dateOperation;
    }

    public function setDateOperation(?\DateTimeInterface $dateOperation): self
    {
        $this->dateOperation = $dateOperation;

        return $this;
    }

    public function getNumOperation(): ?int
    {
        return $this->numOperation;
    }

    public function setNumOperation(?int $numOperation): self
    {
        $this->numOperation = $numOperation;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?string $modePaiement): self
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getNumRP(): ?int
    {
        return $this->numRP;
    }

    public function setNumRP(?int $numRP): self
    {
        $this->numRP = $numRP;

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

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

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

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(?string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getEmetteur(): ?string
    {
        return $this->emetteur;
    }

    public function setEmetteur(?string $emetteur): self
    {
        $this->emetteur = $emetteur;

        return $this;
    }
}
