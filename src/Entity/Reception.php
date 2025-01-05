<?php

namespace App\Entity;

use App\Repository\ReceptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceptionRepository::class)]
class Reception
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numReception = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateReception = null;

    #[ORM\ManyToOne(inversedBy: 'receptions')]
    private ?Fournisseur $fournisseur = null;

    #[ORM\Column(length: 255)]
    private ?string $RaisonSociale = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NumLivraison = null;

    #[ORM\Column(nullable: true)]
    private ?bool $ExonereTVA = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $BC_AO_Autre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NumBC_AO = null;

    #[ORM\Column(nullable: true)]
    private ?bool $Neuf = null;

    #[ORM\Column(nullable: true)]
    private ?float $TotalHT = null;

    #[ORM\Column(nullable: true)]
    private ?float $TotalTVA = null;

    #[ORM\Column(nullable: true)]
    private ?float $TotalTTC = null;

    #[ORM\OneToMany(mappedBy: 'reception', targetEntity: ReceptionLigne::class, orphanRemoval: true, cascade:["persist","remove"])]
    private Collection $receptionLignes;

    public function __construct()
    {
        $this->receptionLignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNumReception(): ?string
    {
        return $this->numReception;
    }

    public function setNumReception(string $numReception): self
    {
        $this->numReception = $numReception;

        return $this;
    }

    public function getDateReception(): ?\DateTimeInterface
    {
        return $this->DateReception;
    }

    public function setDateReception(\DateTimeInterface $DateReception): self
    {
        $this->DateReception = $DateReception;

        return $this;
    }

    public function getFournisseur(): ?fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?fournisseur $fournisseur): self
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->RaisonSociale;
    }

    public function setRaisonSociale(string $RaisonSociale): self
    {
        $this->RaisonSociale = $RaisonSociale;

        return $this;
    }

    public function getNumLivraison(): ?string
    {
        return $this->NumLivraison;
    }

    public function setNumLivraison(?string $NumLivraison): self
    {
        $this->NumLivraison = $NumLivraison;

        return $this;
    }

    public function isExonereTVA(): ?bool
    {
        return $this->ExonereTVA;
    }

    public function setExonereTVA(?bool $ExonereTVA): self
    {
        $this->ExonereTVA = $ExonereTVA;

        return $this;
    }

    public function getBCAOAutre(): ?string
    {
        return $this->BC_AO_Autre;
    }

    public function setBCAOAutre(?string $BC_AO_Autre): self
    {
        $this->BC_AO_Autre = $BC_AO_Autre;

        return $this;
    }

    public function getNumBCAO(): ?string
    {
        return $this->NumBC_AO;
    }

    public function setNumBCAO(?string $NumBC_AO): self
    {
        $this->NumBC_AO = $NumBC_AO;

        return $this;
    }

    public function isNeuf(): ?bool
    {
        return $this->Neuf;
    }

    public function setNeuf(?bool $Neuf): self
    {
        $this->Neuf = $Neuf;

        return $this;
    }

    public function getTotalHT(): ?float
    {
        return $this->TotalHT;
    }

    public function setTotalHT(?float $TotalHT): self
    {
        $this->TotalHT = $TotalHT;

        return $this;
    }

    public function getTotalTVA(): ?float
    {
        return $this->TotalTVA;
    }

    public function setTotalTVA(?float $TotalTVA): self
    {
        $this->TotalTVA = $TotalTVA;

        return $this;
    }

    public function getTotalTTC(): ?float
    {
        return $this->TotalTTC;
    }

    public function setTotalTTC(?float $TotalTTC): self
    {
        $this->TotalTTC = $TotalTTC;

        return $this;
    }

    /**
     * @return Collection<int, ReceptionLigne>
     */
    public function getReceptionLignes(): Collection
    {
        return $this->receptionLignes;
    }

    public function addReceptionLigne(ReceptionLigne $receptionLigne): self
    {
        if (!$this->receptionLignes->contains($receptionLigne)) {
            $this->receptionLignes->add($receptionLigne);
            $receptionLigne->setReception($this);
        }

        return $this;
    }

    public function removeReceptionLigne(ReceptionLigne $receptionLigne): self
    {
        if ($this->receptionLignes->removeElement($receptionLigne)) {
            // set the owning side to null (unless already changed)
            if ($receptionLigne->getReception() === $this) {
                $receptionLigne->setReception(null);
            }
        }

        return $this;
    }
}
