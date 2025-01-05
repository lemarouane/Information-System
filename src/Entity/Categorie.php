<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $designation = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: SousCategorie::class, orphanRemoval: true)]
    private Collection $sousCategories;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: Article::class)]
    private Collection $articles;

    #[ORM\OneToMany(mappedBy: 'Categorie', targetEntity: RegistreInventaire::class)]
    private Collection $registreInventaires;

    public function __construct()
    {
        $this->sousCategories = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->registreInventaires = new ArrayCollection();
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

    public function getdesignation(): ?string
    {
        return $this->designation;
    }

    public function setdesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * @return Collection<int, SousCategorie>
     */
    public function getSousCategories(): Collection
    {
        return $this->sousCategories;
    }

    public function addSousCategory(SousCategorie $sousCategory): self
    {
        if (!$this->sousCategories->contains($sousCategory)) {
            $this->sousCategories->add($sousCategory);
            $sousCategory->setCategorie($this);
        }

        return $this;
    }

    public function removeSousCategory(SousCategorie $sousCategory): self
    {
        if ($this->sousCategories->removeElement($sousCategory)) {
            // set the owning side to null (unless already changed)
            if ($sousCategory->getCategorie() === $this) {
                $sousCategory->setCategorie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setCategorie($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getCategorie() === $this) {
                $article->setCategorie(null);
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
            $registreInventaire->setCategorie($this);
        }

        return $this;
    }

    public function removeRegistreInventaire(RegistreInventaire $registreInventaire): self
    {
        if ($this->registreInventaires->removeElement($registreInventaire)) {
            // set the owning side to null (unless already changed)
            if ($registreInventaire->getCategorie() === $this) {
                $registreInventaire->setCategorie(null);
            }
        }

        return $this;
    }
}
