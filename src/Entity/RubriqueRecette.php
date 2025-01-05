<?php

namespace App\Entity;

use App\Repository\RubriqueRecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RubriqueRecetteRepository::class)]
class RubriqueRecette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 10)]
    private ?string $nature = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant = null;


    #[ORM\Column(length: 4, nullable: true)]
    private ?string $annee = null;

    #[ORM\ManyToOne(inversedBy: 'rubriqueRecettes')]
    private ?Recette $rubrique = null;

    #[ORM\OneToMany(mappedBy: 'rubrique', targetEntity: Paiement::class)]
    private Collection $paiements;

    #[ORM\OneToMany(mappedBy: 'rubrique', targetEntity: Paiementprojet::class)]
    private Collection $paiementprojets;

    #[ORM\OneToMany(mappedBy: 'rubrique', targetEntity: Paiementdivers::class)]
    private Collection $paiementdivers;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
        $this->paiementprojets = new ArrayCollection();
        $this->paiementdivers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getNature(): ?string
    {
        return $this->nature;
    }

    public function setNature(string $nature): self
    {
        $this->nature = $nature;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): self
    {
        $this->montant = $montant;

        return $this;
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

    public function getRubrique(): ?Recette
    {
        return $this->rubrique;
    }

    public function setRubrique(?Recette $rubrique): self
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setRubrique($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getRubrique() === $this) {
                $paiement->setRubrique(null);
            }
        }

        return $this;
    }



 /**
     * @return Collection<int, Paiementprojet>
     */
    public function getPaiementprojets(): Collection
    {
        return $this->paiementprojets;
    }

    public function addPaiementprojet(Paiementprojet $paiementprojet): self
    {
        if (!$this->paiementprojets->contains($paiementprojet)) {
            $this->paiementprojets->add($paiementprojet);
            $paiementprojet->setRubrique($this);
        }

        return $this;
    }

    public function removePaiementprojet(Paiementprojet $paiementprojet): self
    {
        if ($this->paiementprojets->removeElement($paiementprojet)) {
            // set the owning side to null (unless already changed)
            if ($paiementprojet->getRubrique() === $this) {
                $paiementprojet->setRubrique(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Paiementdivers>
     */
    public function getPaiementdivers(): Collection
    {
        return $this->paiementdivers;
    }

    public function addPaiementdiver(Paiementdivers $paiementdiver): self
    {
        if (!$this->paiementdivers->contains($paiementdiver)) {
            $this->paiementdivers->add($paiementdiver);
            $paiementdiver->setRubrique($this);
        }

        return $this;
    }

    public function removePaiementdiver(Paiementdivers $paiementdiver): self
    {
        if ($this->paiementdivers->removeElement($paiementdiver)) {
            // set the owning side to null (unless already changed)
            if ($paiementdiver->getRubrique() === $this) {
                $paiementdiver->setRubrique(null);
            }
        }

        return $this;
    }




}
