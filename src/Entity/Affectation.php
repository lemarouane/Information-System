<?php

namespace App\Entity;

use App\Repository\AffectationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AffectationRepository::class)]
class Affectation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'affectations')]
    private ?Article $Article = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NumInventaire = null;

    #[ORM\ManyToOne(inversedBy: 'affectations')]
    private ?Personnel $Personnel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $local = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateFin = null;

    #[ORM\ManyToOne(inversedBy: 'affectations')]
    private ?RegistreInventaire $Inventaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $qte = null;

    #[ORM\ManyToOne(inversedBy: 'affectations', targetEntity: Decharge::class,  cascade:["persist","remove"])]
    private ?Decharge $Decharge = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->Article;
    }

    public function setArticle(?Article $Article): self
    {
        $this->Article = $Article;

        return $this;
    }

    public function getNumInventaire(): ?string
    {
        return $this->NumInventaire;
    }

    public function setNumInventaire(string $NumInventaire): self
    {
        $this->NumInventaire = $NumInventaire;

        return $this;
    }

    public function getPersonnel(): ?Personnel
    {
        return $this->Personnel;
    }

    public function setPersonnel(?Personnel $Personnel): self
    {
        $this->Personnel = $Personnel;

        return $this;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(?string $local): self
    {
        $this->local = $local;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->DateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $DateDebut): self
    {
        $this->DateDebut = $DateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(?\DateTimeInterface $DateFin): self
    {
        $this->DateFin = $DateFin;

        return $this;
    }

    public function getInventaire(): ?RegistreInventaire
    {
        return $this->Inventaire;
    }

    public function setInventaire(?RegistreInventaire $Inventaire): self
    {
        $this->Inventaire = $Inventaire;

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(?int $qte): self
    {
        $this->qte = $qte;

        return $this;
    }

    public function getDecharge(): ?Decharge
    {
        return $this->Decharge;
    }

    public function setDecharge(?Decharge $Decharge): self
    {
        $this->Decharge = $Decharge;

        return $this;
    }
}
