<?php

namespace App\Entity;

use App\Repository\NoteFonctionnaireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteFonctionnaireRepository::class)]
class NoteFonctionnaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $note1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $note2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $note3 = null;

    #[ORM\Column(nullable: true)]
    private ?int $note4 = null;

    #[ORM\Column(nullable: true)]
    private ?int $note5 = null;

    #[ORM\Column(nullable: true)]
    private ?int $noteAnuelle = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $remarque = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien = null;

    #[ORM\ManyToOne(inversedBy: 'noteFonctionnaires')]
    private ?Personnel $personnel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote1(): ?int
    {
        return $this->note1;
    }

    public function setNote1(?int $note1): self
    {
        $this->note1 = $note1;

        return $this;
    }

    public function getNote2(): ?int
    {
        return $this->note2;
    }

    public function setNote2(?int $note2): self
    {
        $this->note2 = $note2;

        return $this;
    }

    public function getNote3(): ?int
    {
        return $this->note3;
    }

    public function setNote3(int $note3): self
    {
        $this->note3 = $note3;

        return $this;
    }
    public function getNote4(): ?int
    {
        return $this->note4;
    }

    public function setNote4(int $note4): self
    {
        $this->note4 = $note4;

        return $this;
    }
    public function getNote5(): ?int
    {
        return $this->note5;
    }

    public function setNote5(?int $note5): self
    {
        $this->note5 = $note5; 

        return $this;
    }

    public function getNoteAnuelle(): ?int
    {
        return $this->noteAnuelle;
    }

    public function setNoteAnuelle(?int $noteAnuelle): self
    {
        $this->noteAnuelle = $noteAnuelle;

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

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(?string $remarque): self
    {
        $this->remarque = $remarque;

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

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;

        return $this;
    }
}
