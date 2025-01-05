<?php

namespace App\Entity\Etudiant;

use App\Repository\ClubsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClubsRepository::class)]
#[ORM\Table(name: 'clubs')] 
class Clubs
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
    private ?string $nomassoc = null; // nomassoc paysVille

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $posteoccupe = null; // posteoccupe organisme

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null; // description poste

   

    #[ORM\ManyToOne(inversedBy: 'clubs', cascade:['persist'])]
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

    public function getNomassoc(): ?string
    {
        return $this->nomassoc;
    }

    public function setNomassoc(?string $nomassoc): self
    {
        $this->nomassoc = $nomassoc;

        return $this;
    }

    public function getPosteoccupe(): ?string
    {
        return $this->posteoccupe;
    }

    public function setPosteoccupe(?string $posteoccupe): self
    {
        $this->posteoccupe = $posteoccupe;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
