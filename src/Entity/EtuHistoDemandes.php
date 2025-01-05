<?php

namespace App\Entity;


use App\Repository\EtuHistoDemandesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtuHistoDemandesRepository::class)]
class EtuHistoDemandes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type_demande = null;

    #[ORM\Column]
    private ?int $id_demande = null;


    #[ORM\ManyToOne(inversedBy: 'etuHistoDemandes')]
    private ?Personnel $validateur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnvoie = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateValidation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $niveau = null;

    #[ORM\Column(length: 4)]
    private ?string $annee_univ = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeDemande(): ?string
    {
        return $this->type_demande;
    }

    public function setTypeDemande(string $type_demande): self
    {
        $this->type_demande = $type_demande;

        return $this;
    }

    public function getIdDemande(): ?int
    {
        return $this->id_demande;
    }

    public function setIdDemande(int $id_demande): self
    {
        $this->id_demande = $id_demande;

        return $this;
    }

   

    public function getValidateur(): ?Personnel
    {
        return $this->validateur;
    }

    public function setValidateur(?Personnel $validateur): self
    {
        $this->validateur = $validateur;

        return $this;
    }

    public function getDateEnvoie(): ?\DateTimeInterface
    {
        return $this->dateEnvoie;
    }

    public function setDateEnvoie(\DateTimeInterface $dateEnvoie): self
    {
        $this->dateEnvoie = $dateEnvoie;

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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

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

    public function getAnneeUniv(): ?string
    {
        return $this->annee_univ;
    }

    public function setAnneeUniv(string $annee_univ): self
    {
        $this->annee_univ = $annee_univ;

        return $this;
    }
}
