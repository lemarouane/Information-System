<?php

namespace App\Entity;

use App\Repository\LigneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneRepository::class)]
#[ORM\Table(name: 'ligne')]
class Ligne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 4, name: 'numLigne')]
    private ?string $numLigne = null;

    #[ORM\Column(length: 40)] 
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'ligne')]
    private ?Paragraphe $paragraphe = null;

    #[ORM\ManyToOne(inversedBy: 'lignes')]
    #[ORM\JoinColumn(name: 'articlePE_id')]
    private ?ArticlePE $articlePE = null;

    #[ORM\OneToMany(mappedBy: 'ligne', targetEntity: Rubrique::class)]
    private Collection $rubrique;

    public function __construct()
    {
        $this->rubrique = new ArrayCollection();
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

    public function getNumLigne(): ?string
    {
        return $this->numLigne;
    }

    public function setNumLigne(string $numLigne): self
    {
        $this->numLigne = $numLigne;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getParagraphe(): ?Paragraphe
    {
        return $this->paragraphe;
    }

    public function setParagraphe(?Paragraphe $paragraphe): self
    {
        $this->paragraphe = $paragraphe;

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
     * @return Collection<int, Rubrique>
     */
    public function getRubrique(): Collection
    {
        return $this->rubrique;
    }

    public function addRubrique(Rubrique $rubrique): self
    {
        if (!$this->rubrique->contains($rubrique)) {
            $this->rubrique->add($rubrique);
            $rubrique->setLigne($this);
        }

        return $this;
    }

    public function removeRubrique(Rubrique $rubrique): self
    {
        if ($this->rubrique->removeElement($rubrique)) {
            // set the owning side to null (unless already changed)
            if ($rubrique->getLigne() === $this) {
                $rubrique->setLigne(null);
            }
        }

        return $this;
    }
}
