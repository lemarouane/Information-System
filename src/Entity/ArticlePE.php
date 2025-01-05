<?php

namespace App\Entity;

use App\Repository\ArticlePERepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArticlePERepository::class)]
#[ORM\Table(name: 'articlepe')]
class ArticlePE
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255, name: 'numArticle')]
    private ?string $numArticle = null;

    #[ORM\OneToMany(mappedBy: 'articlePE', targetEntity: Paragraphe::class)]
    private Collection $paragraphe;

    #[ORM\OneToMany(mappedBy: 'articlePE', targetEntity: Ligne::class)]
    private Collection $lignes;

    #[ORM\OneToMany(mappedBy: 'articlePE', targetEntity: Rubrique::class)]
    private Collection $rubriques;

    public function __construct()
    {
        $this->paragraphe = new ArrayCollection();
        $this->lignes = new ArrayCollection();
        $this->rubriques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumArticle(): ?string
    {
        return $this->numArticle;
    }

    public function setNumArticle(string $numArticle): self
    {
        $this->numArticle = $numArticle;

        return $this;
    }

    /**
     * @return Collection<int, Paragraphe>
     */
    public function getParagraphe(): Collection
    {
        return $this->paragraphe;
    }

    public function addParagraphe(Paragraphe $paragraphe): self
    {
        if (!$this->paragraphe->contains($paragraphe)) {
            $this->paragraphe->add($paragraphe);
            $paragraphe->setArticlePE($this);
        }

        return $this;
    }

    public function removeParagraphe(Paragraphe $paragraphe): self
    {
        if ($this->paragraphe->removeElement($paragraphe)) {
            // set the owning side to null (unless already changed)
            if ($paragraphe->getArticlePE() === $this) {
                $paragraphe->setArticlePE(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ligne>
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(Ligne $ligne): self
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setArticlePE($this);
        }

        return $this;
    }

    public function removeLigne(Ligne $ligne): self
    {
        if ($this->lignes->removeElement($ligne)) {
            // set the owning side to null (unless already changed)
            if ($ligne->getArticlePE() === $this) {
                $ligne->setArticlePE(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rubrique>
     */
    public function getRubriques(): Collection
    {
        return $this->rubriques;
    }

    public function addRubrique(Rubrique $rubrique): self
    {
        if (!$this->rubriques->contains($rubrique)) {
            $this->rubriques->add($rubrique);
            $rubrique->setArticlePE($this);
        }

        return $this;
    }

    public function removeRubrique(Rubrique $rubrique): self
    {
        if ($this->rubriques->removeElement($rubrique)) {
            // set the owning side to null (unless already changed)
            if ($rubrique->getArticlePE() === $this) {
                $rubrique->setArticlePE(null);
            }
        }

        return $this;
    }
}
