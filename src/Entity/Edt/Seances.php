<?php

namespace App\Entity\Edt;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Edt\SeancesRepository;

#[ORM\Entity(repositoryClass: SeancesRepository::class)]
#[ORM\Table(name: 'seances')]
class Seances
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codeseance')]
    private $codeseance;

    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $dateSeance = null; // "default"="0000-00-00"
 
    #[ORM\Column( nullable: false)]
    private ?int $heureSeance = 800;
  
    #[ORM\Column( nullable: false)]
    private ?int $dureeSeance = 100;

    #[ORM\Column( nullable: false)]
    private ?int $codeEnseignement = 0;

    #[ORM\Column(nullable: false )]
    private ?\DateTimeImmutable $dateModif = null; 

    #[ORM\Column(nullable: false )]
    private ?\DateTimeImmutable $dateCreation = null; // "default"="0000-00-00 00:00:00"

    #[ORM\Column(nullable: false)]
    private ?bool $deleted = false;


    #[ORM\Column(nullable: false)]
    private ?int $codeProprietaire = 0;
  
    #[ORM\Column(length: 200, nullable: true)]
    private ?string $commentaire = ''; // options={"fixed"=true}

    #[ORM\Column(nullable: false)]
    private ?bool $bloquee = false;
   
    
    #[ORM\Column( nullable: false)]
    private ?bool $diffusable = true;

    #[ORM\Column( nullable: false)]
    private ?bool $annulee = false;

   

    public function getCodeseance(): ?int
    {
        return $this->codeSeance;
    }

    public function getDateseance(): ?\DateTimeInterface
    {
        return $this->dateSeance;
    }

    public function setDateseance(\DateTimeInterface $dateSeance): self
    {
        $this->dateSeance = $dateSeance;

        return $this;
    }

    public function getHeureseance(): ?int
    {
        return $this->heureSeance;
    }

    public function setHeureseance(int $heureSeance): self
    {
        $this->heureSeance = $heureSeance;

        return $this;
    }

    public function getDureeseance(): ?int
    {
        return $this->dureeSeance;
    }

    public function setDureeseance(int $dureeSeance): self
    {
        $this->dureeSeance = $dureeSeance;

        return $this;
    }

    public function getCodeenseignement(): ?int
    {
        return $this->codeEnseignement;
    }

    public function setCodeenseignement(int $codeEnseignement): self
    {
        $this->codeEnseignement = $codeEnseignement;

        return $this;
    }

    public function getDatemodif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    public function setDatemodif(\DateTimeInterface $dateModif): self
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDatecreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getCodeproprietaire(): ?int
    {
        return $this->codeProprietaire;
    }

    public function setCodeproprietaire(int $codeProprietaire): self
    {
        $this->codeProprietaire = $codeProprietaire;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getBloquee(): ?bool
    {
        return $this->bloquee;
    }

    public function setBloquee(bool $bloquee): self
    {
        $this->bloquee = $bloquee;

        return $this;
    }

    public function getDiffusable(): ?bool
    {
        return $this->diffusable;
    }

    public function setDiffusable(bool $diffusable): self
    {
        $this->diffusable = $diffusable;

        return $this;
    }

    public function getAnnulee(): ?bool
    {
        return $this->annulee;
    }

    public function setAnnulee(bool $annulee): self
    {
        $this->annulee = $annulee;

        return $this;
    }


}
