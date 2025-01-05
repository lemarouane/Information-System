<?php

namespace App\Entity\Etudiant;

use App\Repository\InscritEtudiantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscritEtudiantRepository::class)]
#[ORM\Table(name: 'inscritetudiant')] 
class InscritEtudiant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'inscritEtudiants')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EtudiantDD $inscription = null;

    #[ORM\Column(length: 255)]
    private ?string $annee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInscription(): ?EtudiantDD
    {
        return $this->inscription;
    }

    public function setInscription(?EtudiantDD $inscription): self
    {
        $this->inscription = $inscription;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(string $annee): self
    {
        $this->annee = $annee;

        return $this;
    }
}
