<?php

namespace App\Entity;

use App\Repository\ProgrammeEmploiRestantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeEmploiRestantRepository::class)]
class ProgrammeEmploiRestant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $intitule = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $annee = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 25, scale: 2, nullable: true)]
    private ?string $montant = null;

    #[ORM\ManyToOne(inversedBy: 'programmeEmploiRestants')]
    private ?ArticlePE $articlePE = null;

    #[ORM\OneToMany(mappedBy: 'programme', targetEntity: ProgrammeElementRestant::class  , cascade:["persist","remove"]) ]
    private Collection $programmeElementRestants;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 2, nullable: true)]
    private ?string $montantReste = null;

    public function __construct()
    {
        $this->programmeElementRestants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(?string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getAnnee(): ?string
    {
        return $this->annee;
    }

    public function setAnnee(?string $annee): self
    {
        $this->annee = $annee;

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
     * @return Collection<int, ProgrammeElementRestant>
     */
    public function getProgrammeElementRestants(): Collection
    {
        return $this->programmeElementRestants;
    }

    public function addProgrammeElementRestant(ProgrammeElementRestant $programmeElementRestant): self
    {
        if (!$this->programmeElementRestants->contains($programmeElementRestant)) {
            $this->programmeElementRestants->add($programmeElementRestant);
            $programmeElementRestant->setProgramme($this);
        }

        return $this;
    }

    public function removeProgrammeElementRestant(ProgrammeElementRestant $programmeElementRestant): self
    {
        if ($this->programmeElementRestants->removeElement($programmeElementRestant)) {
            // set the owning side to null (unless already changed)
            if ($programmeElementRestant->getProgramme() === $this) {
                $programmeElementRestant->setProgramme(null);
            }
        }

        return $this;
    }

    public function getMontantReste(): ?string
    {
        return $this->montantReste;
    }

    public function setMontantReste(?string $montantReste): self
    {
        $this->montantReste = $montantReste;

        return $this;
    }
}
