<?php

namespace App\Entity;

use App\Repository\ParagrapheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParagrapheRepository::class)]
#[ORM\Table(name: 'paragraphe')]
class Paragraphe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255, name: 'numParagraphe')]
    private ?string $numParagraphe = null;

    #[ORM\ManyToOne(inversedBy: 'paragraphe')]
    #[ORM\JoinColumn(name: 'articlePE_id')]
    private ?ArticlePE $articlePE = null;

    #[ORM\OneToMany(mappedBy: 'paragraphe', targetEntity: Ligne::class)]
    private Collection $ligne;

    #[ORM\OneToMany(mappedBy: 'paragraphe', targetEntity: Rubrique::class)]
    private Collection $rubriques;

    public function __construct()
    {
        $this->ligne = new ArrayCollection();
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

    public function getNumParagraphe(): ?string
    {
        return $this->numParagraphe;
    }

    public function setNumParagraphe(string $numParagraphe): self
    {
        $this->numParagraphe = $numParagraphe;

        return $this;
    }

    public function getArticlePE(): ?ArticlePE
    {
        return $this->articlePE;
    }

    public function setArticlePE(?ArticlePE $articlePE): self
    {
        $this->articlePE = $articlePE;

        return $this;
    }

    /**
     * @return Collection<int, Ligne>
     */
    public function getLigne(): Collection
    {
        return $this->ligne;
    }

    public function addLigne(Ligne $ligne): self
    {
        if (!$this->ligne->contains($ligne)) {
            $this->ligne->add($ligne);
            $ligne->setParagraphe($this);
        }

        return $this;
    }

    public function removeLigne(Ligne $ligne): self
    {
        if ($this->ligne->removeElement($ligne)) {
            // set the owning side to null (unless already changed)
            if ($ligne->getParagraphe() === $this) {
                $ligne->setParagraphe(null);
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
            $rubrique->setParagraphe($this);
        }

        return $this;
    }

    public function removeRubrique(Rubrique $rubrique): self
    {
        if ($this->rubriques->removeElement($rubrique)) {
            // set the owning side to null (unless already changed)
            if ($rubrique->getParagraphe() === $this) {
                $rubrique->setParagraphe(null);
            }
        }

        return $this;
    }


}
