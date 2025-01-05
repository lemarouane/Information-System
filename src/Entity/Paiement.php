<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE,nullable: true,name: "datePaiement")]
    private ?\DateTimeInterface $datePaiement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true,name: "dateOperation")]
    private ?\DateTimeInterface $dateOperation = null;

    #[ORM\Column(length: 255, nullable: true,name: "numOperation")]
    private ?string $numOperation = null;

    #[ORM\Column(length: 255, nullable: true,name: "numCheque")]
    private ?string $numCheque = null;

    #[ORM\Column(length: 255,name: "modePaiement")]
    private ?string $modePaiement = null;

    #[ORM\Column(length: 255,name: "type")]
    private ?string $type = null;

    #[ORM\Column(name: "montant")]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    private ?Personnel $responsable = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    private ?RubriqueRecette $rubrique = null;

    #[ORM\Column(length: 20,name: "demandeur")]
    private ?string $demandeur = null;

    #[ORM\Column(length: 4,name: "annee")]
    private ?string $annee = null;

    #[ORM\Column(length: 255, nullable: true,name: "formation")]
    private ?string $formation = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cin = null;

    #[ORM\Column]
    private ?int $numRP = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien = null;

    #[ORM\Column(nullable: true)]
    private ?int $tranche = null;

    #[ORM\Column(nullable: true)]
    private ?bool $lastrp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tiers = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneeuniv = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatepaiement(\DateTimeInterface $datePaiement): self
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

    public function getNumOperation(): ?string
    {
        return $this->numOperation;
    }

    public function setNumOperation(?string $numOperation): self
    {
        $this->numOperation = $numOperation;

        return $this;
    }

    public function getNumCheque(): ?string
    {
        return $this->numCheque;
    }

    public function setNumCheque(?string $numCheque): self
    {
        $this->numCheque = $numCheque;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(string $modePaiement): self
    {
        $this->modePaiement = $modePaiement;

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

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
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

    public function getDemandeur(): ?string
    {
        return $this->demandeur;
    }

    public function setDemandeur(string $demandeur): self
    {
        $this->demandeur = $demandeur;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getFormation(): ?string
    {
        return $this->formation;
    }

    public function setFormation(?string $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getNumRP(): ?int
    {
        return $this->numRP;
    }

    public function setNumRP(int $numRP): self
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

    public function getTranche(): ?int
    {
        return $this->tranche;
    }

    public function setTranche(?int $tranche): self
    {
        $this->tranche = $tranche;

        return $this;
    }

    public function isLastrp(): ?bool
    {
        return $this->lastrp;
    }

    public function setLastrp(?bool $lastrp): self
    {
        $this->lastrp = $lastrp;

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

    public function getTiers(): ?string
    {
        return $this->tiers;
    }

    public function setTiers(?string $tiers): self
    {
        $this->tiers = $tiers;

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
