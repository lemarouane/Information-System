<?php

namespace App\Entity;

use App\Repository\RegistreInventaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegistreInventaireRepository::class)]
class RegistreInventaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $DateReception = null;

    #[ORM\ManyToOne(inversedBy: 'registreInventaires')]
    private ?Article $Article = null;

    #[ORM\ManyToOne(inversedBy: 'registreInventaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ReceptionLigne $receptionLigne = null;

    #[ORM\ManyToOne(inversedBy: 'registreInventaires')]
    private ?Categorie $Categorie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NumBC_AO = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NumLivraison = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $raisonSocialeFournisseur = null;

    #[ORM\Column(nullable: true)]
    private ?float $qte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etatConservation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $affecterA = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $local = null;

    #[ORM\ManyToOne(inversedBy: 'registreInventaires')]
    private ?Personnel $Personnel = null;

    #[ORM\Column(length: 255)]
    private ?string $NumInventaire = null;

    #[ORM\OneToMany(mappedBy: 'Inventaire', targetEntity: Affectation::class)]
    private Collection $affectations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $remarque = null;

    #[ORM\ManyToMany(targetEntity: Decharge::class, mappedBy: 'inventaire')]
    private Collection $decharges;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateDecharge = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $NumDecharge = null;

    public function __construct()
    {
        $this->affectations = new ArrayCollection();
        $this->decharges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getArticle(): ?Article
    {
        return $this->Article;
    }

    public function setArticle(?Article $Article): self
    {
        $this->Article = $Article;

        return $this;
    }

    public function getReceptionLigne(): ?ReceptionLigne
    {
        return $this->receptionLigne;
    }

    public function setReceptionLigne(?ReceptionLigne $receptionLigne): self
    {
        $this->receptionLigne = $receptionLigne;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->Categorie;
    }

    public function setCategorie(?Categorie $Categorie): self
    {
        $this->Categorie = $Categorie;

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

    public function getNumLivraison(): ?string
    {
        return $this->NumLivraison;
    }

    public function setNumLivraison(?string $NumLivraison): self
    {
        $this->NumLivraison = $NumLivraison;

        return $this;
    }

    public function getRaisonSocialeFournisseur(): ?string
    {
        return $this->raisonSocialeFournisseur;
    }

    public function setRaisonSocialeFournisseur(?string $raisonSocialeFournisseur): self
    {
        $this->raisonSocialeFournisseur = $raisonSocialeFournisseur;

        return $this;
    }

    public function getQte(): ?float
    {
        return $this->qte;
    }

    public function setQte(?float $qte): self
    {
        $this->qte = $qte;

        return $this;
    }

    public function getEtatConservation(): ?string
    {
        return $this->etatConservation;
    }

    public function setEtatConservation(?string $etatConservation): self
    {
        $this->etatConservation = $etatConservation;

        return $this;
    }

    public function getAffecterA(): ?string
    {
        return $this->affecterA;
    }

    public function setAffecterA(?string $affecterA): self
    {
        $this->affecterA = $affecterA;

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

    public function getPersonnel(): ?Personnel
    {
        return $this->Personnel;
    }

    public function setPersonnel(?Personnel $Personnel): self
    {
        $this->Personnel = $Personnel;

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

    /**
     * @return Collection<int, Affectation>
     */
    public function getAffectations(): Collection
    {
        return $this->affectations;
    }

    public function addAffectation(Affectation $affectation): self
    {
        if (!$this->affectations->contains($affectation)) {
            $this->affectations->add($affectation);
            $affectation->setInventaire($this);
        }

        return $this;
    }

    public function removeAffectation(Affectation $affectation): self
    {
        if ($this->affectations->removeElement($affectation)) {
            // set the owning side to null (unless already changed)
            if ($affectation->getInventaire() === $this) {
                $affectation->setInventaire(null);
            }
        }

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

    /**
     * @return Collection<int, Decharge>
     */
    public function getDecharges(): Collection
    {
        return $this->decharges;
    }

    public function addDecharge(Decharge $decharge): self
    {
        if (!$this->decharges->contains($decharge)) {
            $this->decharges->add($decharge);
            $decharge->addInventaire($this);
        }

        return $this;
    }

    public function removeDecharge(Decharge $decharge): self
    {
        if ($this->decharges->removeElement($decharge)) {
            $decharge->removeInventaire($this);
        }

        return $this;
    }

    public function getDateDecharge(): ?\DateTimeInterface
    {
        return $this->DateDecharge;
    }

    public function setDateDecharge(?\DateTimeInterface $DateDecharge): self
    {
        $this->DateDecharge = $DateDecharge;

        return $this;
    }

    public function getNumDecharge(): ?string
    {
        return $this->NumDecharge;
    }

    public function setNumDecharge(?string $NumDecharge): self
    {
        $this->NumDecharge = $NumDecharge;

        return $this;
    }
}
