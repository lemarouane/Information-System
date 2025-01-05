<?php

namespace App\Entity\Edt;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Edt\EnseignementsRepository;

#[ORM\Entity(repositoryClass: EnseignementsRepository::class)]
#[ORM\Table(name: 'enseignements')]
class Enseignements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'codeenseignement')]
    private $codeenseignement;


    #[ORM\Column(length: 150, nullable: false)]
    private ?string $nom = 'ENS';

   
    #[ORM\Column( nullable: false)]
    private ?int $codematiere = 0;

   
    #[ORM\Column( nullable: false)]
    private ?int $dureetotale = 0;

    #[ORM\Column( nullable: false)]
    private ?int $dureeseance = 0;

    #[ORM\Column( nullable: false)]
    private ?int $couleurfond = 0;

    #[ORM\Column( nullable: false)]
    private ?int $couleurpolice = 0;
  
    #[ORM\Column(length: 50, nullable: false)]
    private ?string $alias = 'ENS';
   

    #[ORM\Column( nullable: false)]
    private ?int $codetypesalle = 0;
    

    #[ORM\Column( nullable: false)]
    private ?int $codezonesalle = 0;
  
    #[ORM\Column( nullable: false)]
    private ?int $nbseanceshebdo = 0;

    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $datemodif = null; // CURRENT_TIMESTAMP

    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $datecreation = null;

    #[ORM\Column(nullable: false)]
    private ?bool $deleted = false;

    #[ORM\Column( nullable: false)]
    private ?int $codeproprietaire = 0;
  

    #[ORM\Column(length: 150, nullable: false)]
    private ?string $commentaire = '';

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $identifiant = '';

    #[ORM\Column(nullable: false)]
    private ?bool $typepublic = false;

    #[ORM\Column(nullable: false)]
    private ?bool $forfaitaire = false;
    
    #[ORM\Column( nullable: false)]
    private ?int $dureeforfaitaire = 0;
  
    #[ORM\Column( nullable: false)]
    private ?int $volumereparti = 0;

    #[ORM\Column( nullable: false)]
    private ?int $codetypeactivite = 0;

    #[ORM\Column( nullable: false)]
    private ?int $codecomposante =-1;
 
    #[ORM\Column( nullable: false)]
    private ?int $codeniveau =0;

    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $datedebut = null;
   
    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $datefin = null;
   

    

    public function getCodeenseignement(): ?int
    {
        return $this->codeenseignement;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodematiere(): ?int
    {
        return $this->codematiere;
    }

    public function setCodematiere(int $codematiere): self
    {
        $this->codematiere = $codematiere;

        return $this;
    }

    public function getDureetotale(): ?int
    {
        return $this->dureetotale;
    }

    public function setDureetotale(int $dureetotale): self
    {
        $this->dureetotale = $dureetotale;

        return $this;
    }

    public function getDureeseance(): ?int
    {
        return $this->dureeseance;
    }

    public function setDureeseance(int $dureeseance): self
    {
        $this->dureeseance = $dureeseance;

        return $this;
    }

    public function getCouleurfond(): ?int
    {
        return $this->couleurfond;
    }

    public function setCouleurfond(int $couleurfond): self
    {
        $this->couleurfond = $couleurfond;

        return $this;
    }

    public function getCouleurpolice(): ?int
    {
        return $this->couleurpolice;
    }

    public function setCouleurpolice(int $couleurpolice): self
    {
        $this->couleurpolice = $couleurpolice;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getCodetypesalle(): ?int
    {
        return $this->codetypesalle;
    }

    public function setCodetypesalle(int $codetypesalle): self
    {
        $this->codetypesalle = $codetypesalle;

        return $this;
    }

    public function getCodezonesalle(): ?int
    {
        return $this->codezonesalle;
    }

    public function setCodezonesalle(int $codezonesalle): self
    {
        $this->codezonesalle = $codezonesalle;

        return $this;
    }

    public function getNbseanceshebdo(): ?int
    {
        return $this->nbseanceshebdo;
    }

    public function setNbseanceshebdo(int $nbseanceshebdo): self
    {
        $this->nbseanceshebdo = $nbseanceshebdo;

        return $this;
    }

    public function getDatemodif(): ?\DateTimeInterface
    {
        return $this->datemodif;
    }

    public function setDatemodif(\DateTimeInterface $datemodif): self
    {
        $this->datemodif = $datemodif;

        return $this;
    }

    public function getDatecreation(): ?\DateTimeInterface
    {
        return $this->datecreation;
    }

    public function setDatecreation(\DateTimeInterface $datecreation): self
    {
        $this->datecreation = $datecreation;

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
        return $this->codeproprietaire;
    }

    public function setCodeproprietaire(int $codeproprietaire): self
    {
        $this->codeproprietaire = $codeproprietaire;

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

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getTypepublic(): ?bool
    {
        return $this->typepublic;
    }

    public function setTypepublic(bool $typepublic): self
    {
        $this->typepublic = $typepublic;

        return $this;
    }

    public function getForfaitaire(): ?bool
    {
        return $this->forfaitaire;
    }

    public function setForfaitaire(bool $forfaitaire): self
    {
        $this->forfaitaire = $forfaitaire;

        return $this;
    }

    public function getDureeforfaitaire(): ?int
    {
        return $this->dureeforfaitaire;
    }

    public function setDureeforfaitaire(int $dureeforfaitaire): self
    {
        $this->dureeforfaitaire = $dureeforfaitaire;

        return $this;
    }

    public function getVolumereparti(): ?int
    {
        return $this->volumereparti;
    }

    public function setVolumereparti(int $volumereparti): self
    {
        $this->volumereparti = $volumereparti;

        return $this;
    }

    public function getCodetypeactivite(): ?int
    {
        return $this->codetypeactivite;
    }

    public function setCodetypeactivite(int $codetypeactivite): self
    {
        $this->codetypeactivite = $codetypeactivite;

        return $this;
    }

    public function getCodecomposante(): ?int
    {
        return $this->codecomposante;
    }

    public function setCodecomposante(int $codecomposante): self
    {
        $this->codecomposante = $codecomposante;

        return $this;
    }

    public function getCodeniveau(): ?int
    {
        return $this->codeniveau;
    }

    public function setCodeniveau(int $codeniveau): self
    {
        $this->codeniveau = $codeniveau;

        return $this;
    }

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(\DateTimeInterface $datedebut): self
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;

        return $this;
    }


}
