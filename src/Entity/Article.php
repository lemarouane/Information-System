<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fournisseur $fournisseur = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?Categorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    private ?SousCategorie $sousCategorie = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\OneToMany(mappedBy: 'Article', targetEntity: ReceptionLigne::class, orphanRemoval: true)]
    private Collection $receptionLignes;

    #[ORM\OneToMany(mappedBy: 'Article', targetEntity: RegistreInventaire::class)]
    private Collection $registreInventaires;

    #[ORM\OneToMany(mappedBy: 'Article', targetEntity: Affectation::class)]
    private Collection $affectations;

    #[ORM\Column]
    private ?bool $inv = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: DemandeLigne::class)]
    private Collection $demandeLignes;

    #[ORM\Column(nullable: true)]
    private ?int $Qte = null;

    #[ORM\Column(nullable: true)]
    private ?int $Seuil = null;

    public function __construct()
    {
        $this->receptionLignes = new ArrayCollection();
        $this->registreInventaires = new ArrayCollection();
        $this->affectations = new ArrayCollection();
        $this->demandeLignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): self
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getSousCategorie(): ?SousCategorie
    {
        return $this->sousCategorie;
    }

    public function setSousCategorie(?SousCategorie $sousCategorie): self
    {
        $this->sousCategorie = $sousCategorie;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

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
            $receptionLigne->setArticle($this);
        }

        return $this;
    }

    public function removeReceptionLigne(ReceptionLigne $receptionLigne): self
    {
        if ($this->receptionLignes->removeElement($receptionLigne)) {
            // set the owning side to null (unless already changed)
            if ($receptionLigne->getArticle() === $this) {
                $receptionLigne->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RegistreInventaire>
     */
    public function getRegistreInventaires(): Collection
    {
        return $this->registreInventaires;
    }

    public function addRegistreInventaire(RegistreInventaire $registreInventaire): self
    {
        if (!$this->registreInventaires->contains($registreInventaire)) {
            $this->registreInventaires->add($registreInventaire);
            $registreInventaire->setArticle($this);
        }

        return $this;
    }

    public function removeRegistreInventaire(RegistreInventaire $registreInventaire): self
    {
        if ($this->registreInventaires->removeElement($registreInventaire)) {
            // set the owning side to null (unless already changed)
            if ($registreInventaire->getArticle() === $this) {
                $registreInventaire->setArticle(null);
            }
        }

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
            $affectation->setArticle($this);
        }

        return $this;
    }

    public function removeAffectation(Affectation $affectation): self
    {
        if ($this->affectations->removeElement($affectation)) {
            // set the owning side to null (unless already changed)
            if ($affectation->getArticle() === $this) {
                $affectation->setArticle(null);
            }
        }

        return $this;
    }

    public function isInv(): ?bool
    {
        return $this->inv;
    }

    public function setInv(bool $inv): self
    {
        $this->inv = $inv;

        return $this;
    }

    /**
     * @return Collection<int, DemandeLigne>
     */
    public function getDemandeLignes(): Collection
    {
        return $this->demandeLignes;
    }

    public function addDemandeLigne(DemandeLigne $demandeLigne): self
    {
        if (!$this->demandeLignes->contains($demandeLigne)) {
            $this->demandeLignes->add($demandeLigne);
            $demandeLigne->setArticle($this);
        }

        return $this;
    }

    public function removeDemandeLigne(DemandeLigne $demandeLigne): self
    {
        if ($this->demandeLignes->removeElement($demandeLigne)) {
            // set the owning side to null (unless already changed)
            if ($demandeLigne->getArticle() === $this) {
                $demandeLigne->setArticle(null);
            }
        }

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->Qte;
    }

    public function setQte(?int $Qte): self
    {
        $this->Qte = $Qte;

        return $this;
    }

    public function getSeuil(): ?int
    {
        return $this->Seuil;
    }

    public function setSeuil(?int $Seuil): self
    {
        $this->Seuil = $Seuil;

        return $this;
    }
}
