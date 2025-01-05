<?php

namespace App\Entity\Etudiant;

use App\Repository\EtapeAvRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtapeAvRepository::class)]
#[ORM\Table(name: 'etapeav')] 
class EtapeAv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $etapeAnc = null;

    #[ORM\Column(length: 10)]
    private ?string $etapeNouv = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtapeAnc(): ?string
    {
        return $this->etapeAnc;
    }

    public function setEtapeAnc(string $etapeAnc): self
    {
        $this->etapeAnc = $etapeAnc;

        return $this;
    }

    public function getEtapeNouv(): ?string
    {
        return $this->etapeNouv;
    }

    public function setEtapeNouv(string $etapeNouv): self
    {
        $this->etapeNouv = $etapeNouv;

        return $this;
    }
}
