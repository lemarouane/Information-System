<?php

namespace App\Entity;

use App\Repository\ProgrammeElementProjetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeElementProjetRepository::class)]
class ProgrammeElementProjet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'programmeElementProjets')]
    private ?ProgrammeEmploiProjet $programme = null;

    #[ORM\ManyToOne(inversedBy: 'programmeElementProjets')]
    private ?Rubrique $rubrique = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 25, scale: 2, nullable: true)]
    private ?string $montant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgramme(): ?ProgrammeEmploiProjet
    {
        return $this->programme;
    }

    public function setProgramme(?ProgrammeEmploiProjet $programme): self
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

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(?string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }
}
