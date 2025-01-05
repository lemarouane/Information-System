<?php

namespace App\Entity;

use App\Repository\PaiementdiversRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementdiversRepository::class)]
class Paiementdivers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateOperation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $modePaiement = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 2, nullable: true)]
    private ?string $montant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien = null;

    #[ORM\Column(nullable: true)]
    private ?int $rp = null;

    #[ORM\ManyToOne(inversedBy: 'paiementdivers')]
    private ?RubriqueRecette $rubrique = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $com = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 25, scale: 2, nullable: true)]
    private ?string $montantmoinsis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emetteur = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): self
    {
        $this->annee = $annee;

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

    public function getModePaiement(): ?string
    {
        return $this->modePaiement;
    }

    public function setModePaiement(?string $modePaiement): self
    {
        $this->modePaiement = $modePaiement;

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

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    public function getRp(): ?int
    {
        return $this->rp;
    }

    public function setRp(?int $rp): self
    {
        $this->rp = $rp;

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

    public function getCom(): ?string
    {
        return $this->com;
    }

    public function setCom(?string $com): self
    {
        $this->com = $com;

        return $this;
    }

    public function getMontantmoinsis(): ?string
    {
        return $this->montantmoinsis;
    }

    public function setMontantmoinsis(?string $montantmoinsis): self
    {
        $this->montantmoinsis = $montantmoinsis;

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
