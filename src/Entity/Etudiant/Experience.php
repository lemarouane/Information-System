<?php

namespace App\Entity\Etudiant;

use App\Repository\ExperienceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
#[ORM\Table(name: 'experience')] 
class Experience
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $intituleposte = null; // intituleposte paysVille

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $entreprise = null; // entreprise organisme

    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $lieu = null; //lieu poste

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descriptionmission = null; //descriptionmission departement

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $lienportfolio = null; // lienportfolio rhPhone

    #[ORM\ManyToOne(inversedBy: 'experiences', cascade:['persist'])]
    private ?Cvtheque $cvtheque = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getIntituleposte(): ?string
    {
        return $this->intituleposte;
    }

    public function setIntituleposte(?string $intituleposte): self
    {
        $this->intituleposte = $intituleposte;

        return $this;
    }

    public function getEntreprise(): ?string
    {
        return $this->entreprise;
    }

    public function setEntreprise(?string $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDescriptionmission(): ?string
    {
        return $this->descriptionmission;
    }

    public function setDescriptionmission(string $descriptionmission): self
    {
        $this->descriptionmission = $descriptionmission;

        return $this;
    }

    public function getLienportfolio(): ?string
    {
        return $this->lienportfolio;
    }

    public function setLienportfolioe(string $lienportfolio): self
    {
        $this->lienportfolio = $lienportfolio;

        return $this;
    }


    public function getCvtheque(): ?Cvtheque
    {
        return $this->cvtheque;
    }

    public function setCvtheque(?Cvtheque $cvtheque): self
    {
        $this->cvtheque = $cvtheque;

        return $this;
    }
}
