<?php

namespace App\Entity;

use App\Repository\FiliereFcResponsableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiliereFcResponsableRepository::class)]
class FiliereFcResponsable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\ManyToOne(inversedBy: 'filiereFcResponsables')]
    private ?Personnel $responsable = null;

    #[ORM\ManyToOne(inversedBy: 'filiereFcResponsables')]
    private ?FiliereFc $filiere_fc = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getResponsable(): ?Personnel
    {
        return $this->responsable;
    }

    public function setResponsable(?Personnel $responsable): self
    {
        $this->responsable = $responsable;

        return $this;
    }

    public function getFiliereFc(): ?FiliereFc
    {
        return $this->filiere_fc;
    }

    public function setFiliereFc(?FiliereFc $filiere_fc): self
    {
        $this->filiere_fc = $filiere_fc;

        return $this;
    }
}
