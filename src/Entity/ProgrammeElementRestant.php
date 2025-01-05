<?php

namespace App\Entity;

use App\Repository\ProgrammeElementRestantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeElementRestantRepository::class)]
class ProgrammeElementRestant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 25, scale: 2, nullable: true)]
    private ?string $montant = null;

    #[ORM\ManyToOne(inversedBy: 'programmeElementRestants')]
    private ?ProgrammeEmploiRestant $programme = null;

    #[ORM\ManyToOne(inversedBy: 'programmeElementRestants')]
    private ?Rubrique $rubrique = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getProgramme(): ?ProgrammeEmploiRestant
    {
        return $this->programme;
    }

    public function setProgramme(?ProgrammeEmploiRestant $programme): self
    {
        $this->programme = $programme;

        return $this;
    }

    public function getRubrique(): ?Rubrique
    {
        return $this->rubrique;
    }

    public function setRubrique(?Rubrique $rubrique): self
    {
        $this->rubrique = $rubrique;

        return $this;
    }
}
