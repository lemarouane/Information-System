<?php

namespace App\Entity\Etudiant;

use App\Repository\FormationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationsRepository::class)]
#[ORM\Table(name: 'formations')] 
class Formations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $annee = null; // annee paysVille

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $specialite = null; // specialite organisme

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $diplome = null; // diplome poste

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filiere = null; // filiere departement

    #[ORM\ManyToOne(inversedBy: 'formations', cascade:['persist'])]
    private ?Cvtheque $cvtheque = null;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(?string $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(?string $specialite): self
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getDiplome(): ?string
    {
        return $this->diplome;
    }

    public function setDiplome(string $diplome): self
    {
        $this->diplome = $diplome;

        return $this;
    }

    public function getFiliere(): ?string
    {
        return $this->filiere;
    }

    public function setFiliere(string $filiere): self
    {
        $this->filiere = $filiere;

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
