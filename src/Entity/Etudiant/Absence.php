<?php

namespace App\Entity\Etudiant;

use App\Entity\Etudiant\Etudiants;
use App\Repository\AbsenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: AbsenceRepository::class)]
#[ORM\Table(name: 'absence')] 
class Absence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $etape = null;

    #[ORM\Column(length: 255)]
    private ?string $module = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $dateabsence = null;

    #[ORM\Column(length: 255)]
    private ?string $seance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeapgeedebut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeapogeefin = null;

    #[ORM\Column(length: 255)]
    private ?string $idProf = null;


    #[ORM\Column(length: 255)]
    private ?string $justif = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $datedebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $datefin = null;

    #[ORM\Column(length: 255)]
    private ?string $fichier = null;

    #[ORM\Column(length: 255)]
    private ?string $anneeuniv = null;

    #[ORM\Column(length: 255)]
    private ?string $matiere = null;

    #[ORM\ManyToOne(inversedBy: 'absences')]
    private ?Etudiants $idUser = null;

  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtape(): ?string
    {
        return $this->etape;
    }

    public function setEtape(string $etape): self
    {
        $this->etape = $etape;

        return $this;
    }

    public function getModule(): ?string
    {
        return $this->module;
    }

    public function setModule(string $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getDateabsence(): ?\DateTimeInterface
    {
        return $this->dateabsence;
    }

    public function setDateabsence(\DateTimeInterface $dateabsence): self
    {
        $this->dateabsence = $dateabsence;

        return $this;
    }

    public function getSeance(): ?string
    {
        return $this->seance;
    }

    public function setSeance(string $seance): self
    {
        $this->seance = $seance;

        return $this;
    }

    public function getCodeapgeedebut(): ?string
    {
        return $this->codeapgeedebut;
    }

    public function setCodeapgeedebut(?string $codeapgeedebut): self
    {
        $this->codeapgeedebut = $codeapgeedebut;

        return $this;
    }

    public function getCodeapogeefin(): ?string
    {
        return $this->codeapogeefin;
    }

    public function setCodeapogeefin(?string $codeapogeefin): self
    {
        $this->codeapogeefin = $codeapogeefin;

        return $this;
    }

    public function getIdProf(): ?string
    {
        return $this->idProf;
    }

    public function setIdProf(string $idProf): self
    {
        $this->idProf = $idProf;

        return $this;
    }

    public function getJustif(): ?bool
    {
        return $this->justif;
    }

    public function setJustif(?bool $justif): self
    {
        $this->justif = $justif;

        return $this;
    }

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(?\DateTimeInterface $datedebut): self
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(?\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getAnneeuniv(): ?string
    {
        return $this->anneeuniv;
    }

    public function setAnneeuniv(string $anneeuniv): self
    {
        $this->anneeuniv = $anneeuniv;

        return $this;
    }

    public function getMatiere(): ?string
    {
        return $this->matiere;
    }

    public function setMatiere(?string $matiere): self
    {
        $this->matiere = $matiere;

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




}
