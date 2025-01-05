<?php

namespace App\Entity\Etudiant;

use App\Repository\EtatRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
#[ORM\Table(name: 'etat')] 
class Etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $mCP1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $rACHCP1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $aJRCP1 = null;

    #[ORM\Column(nullable: true)]
    private ?int $dERCP1 = null;

    #[ORM\Column(nullable: true)]
    private ?float $mCP2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $rACHCP2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $aJRCP2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $dERCP2 = null;

    #[ORM\Column(nullable: true)]
    private ?float $mcal = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $anneeuniv = null;

    #[ORM\OneToOne(inversedBy: 'etat', cascade: ['persist', 'remove'], fetch:'EAGER')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?Etudiants $user = null;

    #[ORM\OneToOne(mappedBy: 'etat', cascade: ['persist', 'remove'])]
    private ?ChoixAffecter $choixaffecter = null;

    #[ORM\Column]
    private ?int $cCHOIX = null;

    #[ORM\Column]
    private ?int $eRESULTAT = null;

    #[ORM\Column(length: 10)]
    private ?string $codetudiant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMCP1(): ?float
    {
        return $this->mCP1;
    }

    public function setMCP1(?float $mCP1): self
    {
        $this->mCP1 = $mCP1;

        return $this;
    }

    public function getRACHCP1(): ?int
    {
        return $this->rACHCP1;
    }

    public function setRACHCP1(?int $rACHCP1): self
    {
        $this->rACHCP1 = $rACHCP1;

        return $this;
    }

    public function getAJRCP1(): ?int
    {
        return $this->aJRCP1;
    }

    public function setAJRCP1(?int $aJRCP1): self
    {
        $this->aJRCP1 = $aJRCP1;

        return $this;
    }

    public function getDERCP1(): ?int
    {
        return $this->dERCP1;
    }

    public function setDERCP1(?int $dERCP1): self
    {
        $this->dERCP1 = $dERCP1;

        return $this;
    }

    public function getMCP2(): ?float
    {
        return $this->mCP2;
    }

    public function setMCP2(?float $mCP2): self
    {
        $this->mCP2 = $mCP2;

        return $this;
    }

    public function getRACHCP2(): ?int
    {
        return $this->rACHCP2;
    }

    public function setRACHCP2(?int $rACHCP2): self
    {
        $this->rACHCP2 = $rACHCP2;

        return $this;
    }

    public function getAJRCP2(): ?int
    {
        return $this->aJRCP2;
    }

    public function setAJRCP2(?int $aJRCP2): self
    {
        $this->aJRCP2 = $aJRCP2;

        return $this;
    }

    public function getDERCP2(): ?int
    {
        return $this->dERCP2;
    }

    public function setDERCP2(?int $dERCP2): self
    {
        $this->dERCP2 = $dERCP2;

        return $this;
    }

    public function getMcal(): ?float
    {
        return $this->mcal;
    }

    public function setMcal(?float $mcal): self
    {
        $this->mcal = $mcal;

        return $this;
    }

    public function getAnneeuniv(): ?string
    {
        return $this->anneeuniv;
    }

    public function setAnneeuniv(?string $anneeuniv): self
    {
        $this->anneeuniv = $anneeuniv;

        return $this;
    }

    public function getUser(): ?Etudiants
    {
        
        return $this->user;
    }

    public function setUser(?Etudiants $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getChoixaffecter(): ?ChoixAffecter
    {
        return $this->choixaffecter;
    }

    public function setChoixaffecter(?ChoixAffecter $choixaffecter): self
    {
        // unset the owning side of the relation if necessary
        if ($choixaffecter === null && $this->choixaffecter !== null) {
            $this->choixaffecter->setEtat(null);
        }

        // set the owning side of the relation if necessary
        if ($choixaffecter !== null && $choixaffecter->getEtat() !== $this) {
            $choixaffecter->setEtat($this);
        }

        $this->choixaffecter = $choixaffecter;

        return $this;
    }

    public function getCCHOIX(): ?int
    {
        return $this->cCHOIX;
    }

    public function setCCHOIX(int $cCHOIX): self
    {
        $this->cCHOIX = $cCHOIX;

        return $this;
    }

    public function getERESULTAT(): ?int
    {
        return $this->eRESULTAT;
    }

    public function setERESULTAT(int $eRESULTAT): self
    {
        $this->eRESULTAT = $eRESULTAT;

        return $this;
    }

    public function getCodetudiant(): ?string
    {
        return $this->codetudiant;
    }

    public function setCodetudiant(string $codetudiant): self
    {
        $this->codetudiant = $codetudiant;

        return $this;
    }
}
