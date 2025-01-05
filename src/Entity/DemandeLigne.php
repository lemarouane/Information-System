<?php

namespace App\Entity;

use App\Repository\DemandeLigneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DemandeLigneRepository::class)]
class DemandeLigne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'demandeLignes')]
    private ?Article $article = null;

    #[ORM\Column]
    private ?int $Qte = null;

    #[ORM\ManyToOne(inversedBy: 'demandeLignes')]
    private ?Demande $demande = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->Qte;
    }

    public function setQte(int $Qte): self
    {
        $this->Qte = $Qte;

        return $this;
    }

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): self
    {
        $this->demande = $demande;

        return $this;
    }
}
