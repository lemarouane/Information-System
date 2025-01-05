<?php

namespace App\Entity\Etudiant;

use App\Repository\ChoixOrientationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChoixOrientationRepository::class)]
#[ORM\Table(name: 'choixorientation')] 
class ChoixOrientation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4)]
    private ?string $anneeuniv = null;

    #[ORM\Column(length: 40)]
    private ?string $choix1 = null;

    #[ORM\Column(length: 40)]
    private ?string $choix2 = null;

    #[ORM\Column(length: 40)]
    private ?string $choix3 = null;

    #[ORM\Column(length: 40)]
    private ?string $choix4 = null;

    #[ORM\Column(length: 40)]
    private ?string $choix5 = null;

    #[ORM\Column(length: 40)]
    private ?string $choix6 = null;

    #[ORM\Column]
    private ?int $cCHOIX = null;

    #[ORM\OneToOne(inversedBy: 'choix', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiants $user = null;

    #[ORM\Column(length: 10)]
    private ?string $codeEtudiant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnneeuniv(): ?string
    {
        return $this->anneeuniv;
    }

    public function setAnneeuniv(string $anneeuniv): self
    {
        $this->anneeuniv = $anneeuniv;

        return $this;
    }

    public function getChoix1(): ?string
    {
        return $this->choix1;
    }

    public function setChoix1(string $choix1): self
    {
        $this->choix1 = $choix1;

        return $this;
    }

    public function getChoix2(): ?string
    {
        return $this->choix2;
    }

    public function setChoix2(string $choix2): self
    {
        $this->choix2 = $choix2;

        return $this;
    }

    public function getChoix3(): ?string
    {
        return $this->choix3;
    }

    public function setChoix3(string $choix3): self
    {
        $this->choix3 = $choix3;

        return $this;
    }

    public function getChoix4(): ?string
    {
        return $this->choix4;
    }

    public function setChoix4(string $choix4): self
    {
        $this->choix4 = $choix4;

        return $this;
    }

    public function getChoix5(): ?string
    {
        return $this->choix5;
    }

    public function setChoix5(string $choix5): self
    {
        $this->choix5 = $choix5;

        return $this;
    }

    public function getChoix6(): ?string
    {
        return $this->choix6;
    }

    public function setChoix6(string $choix6): self
    {
        $this->choix6 = $choix6;

        return $this;
    }

    public function getCCHOIX(): ?int
    {
        return $this->cCHOIX;
    }

    public function setCCHOIX(int $cCHOIX): self
    {
        $this->cCHOIX = $cCHOIX;

        return $this;
    }

    public function getUser(): ?Etudiants
    {
        return $this->user;
    }

    public function setUser(Etudiants $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCodeEtudiant(): ?string
    {
        return $this->codeEtudiant;
    }

    public function setCodeEtudiant(string $codeEtudiant): self
    {
        $this->codeEtudiant = $codeEtudiant;

        return $this;
    }
}
