<?php

namespace App\Entity;

use App\Repository\ReceptionLigneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReceptionLigneRepository::class)]
class ReceptionLigne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'receptionLignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $Article = null;

    #[ORM\Column]
    private ?float $qte = null;

    #[ORM\ManyToOne(inversedBy: 'receptionLignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Reception $reception = null;

    #[ORM\OneToMany(mappedBy: 'receptionLigne', targetEntity: RegistreInventaire::class, orphanRemoval: true, cascade:["persist","remove"])]
    private Collection $registreInventaires;

    public function __construct()
    {
        $this->registreInventaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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


    public function getQte(): ?float
    {
        return $this->qte;
    }

    public function setQte(float $qte): self
    {
        $this->qte = $qte;

        return $this;
    }


    public function getReception(): ?Reception
    {
        return $this->reception;
    }

    public function setReception(?Reception $reception): self
    {
        $this->reception = $reception;

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
            $registreInventaire->setReceptionLigne($this);
        }

        return $this;
    }

    public function removeRegistreInventaire(RegistreInventaire $registreInventaire): self
    {
        if ($this->registreInventaires->removeElement($registreInventaire)) {
            // set the owning side to null (unless already changed)
            if ($registreInventaire->getReceptionLigne() === $this) {
                $registreInventaire->setReceptionLigne(null);
            }
        }

        return $this;
    }
}
