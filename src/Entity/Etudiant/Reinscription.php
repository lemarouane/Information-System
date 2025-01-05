<?php

namespace App\Entity\Etudiant;

use App\Repository\ReinscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReinscriptionRepository::class)]
#[ORM\Table(name: 'reinscription')] 
class Reinscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDemande = null;

    #[ORM\Column(length: 10)]
    private ?string $etapeAnc = null;

    #[ORM\Column(length: 10)]
    private ?string $annUnivAnc = null;

    #[ORM\Column(length: 10)]
    private ?string $etapeNouv = null;

    #[ORM\Column(length: 10)]
    private ?string $annNouv = null;

    #[ORM\Column(length: 10)]
    private ?string $resultat = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\ManyToOne(inversedBy: 'reinscriptions',cascade: ['persist'])]
    private ?Etudiants $idUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

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

    public function getEtapeAnc(): ?string
    {
        return $this->etapeAnc;
    }

    public function setEtapeAnc(string $etapeAnc): self
    {
        $this->etapeAnc = $etapeAnc;

        return $this;
    }

    public function getAnnUnivAnc(): ?string
    {
        return $this->annUnivAnc;
    }

    public function setAnnUnivAnc(string $annUnivAnc): self
    {
        $this->annUnivAnc = $annUnivAnc;

        return $this;
    }

    public function getEtapeNouv(): ?string
    {
        return $this->etapeNouv;
    }

    public function setEtapeNouv(string $etapeNouv): self
    {
        $this->etapeNouv = $etapeNouv;

        return $this;
    }

    public function getAnnNouv(): ?string
    {
        return $this->annNouv;
    }

    public function setAnnNouv(string $annNouv): self
    {
        $this->annNouv = $annNouv;

        return $this;
    }

    public function getResultat(): ?string
    {
        return $this->resultat;
    }

    public function setResultat(string $resultat): self
    {
        $this->resultat = $resultat;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

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

    public function getIdUser(): ?Etudiants
    {
        return $this->idUser;
    }

    public function setIdUser(?Etudiants $idUser): self
    {
        $this->idUser = $idUser;

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
}
