<?php

namespace App\Entity;

use App\Repository\ProgrammeEmploiProjetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeEmploiProjetRepository::class)]
class ProgrammeEmploiProjet
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

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 2, nullable: true)]
    private ?string $montant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateElab = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateVal = null;

    #[ORM\Column(nullable: true)]
    private ?bool $valider = null;

    #[ORM\Column(nullable: true)]
    private ?bool $activer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $periode = null;

    #[ORM\ManyToOne(inversedBy: 'programmeEmploiProjets')]
    private ?Personnel $personne = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 30, scale: 2, nullable: true)]
    private ?string $montantReste = null;

    #[ORM\OneToMany(mappedBy: 'programme', targetEntity: ProgrammeElementProjet::class  , cascade:["persist","remove"]) ]
    private Collection $programmeElementProjets;

    #[ORM\ManyToOne(inversedBy: 'programmeEmploiProjets')]
    private ?ArticlePE $articlePe = null;

    #[ORM\ManyToOne(inversedBy: 'programmeEmploiProjets')]
    private ?Paragraphe $paragraphe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?int $numpaiementprojet = null;

    public function __construct()
    {
        $this->programmeElementProjets = new ArrayCollection();
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

    public function getDateCre(): ?\DateTimeInterface
    {
        return $this->dateCre;
    }

    public function setDateCre(?\DateTimeInterface $dateCre): self
    {
        $this->dateCre = $dateCre;

        return $this;
    }

    public function getDateElab(): ?\DateTimeInterface
    {
        return $this->dateElab;
    }

    public function setDateElab(?\DateTimeInterface $dateElab): self
    {
        $this->dateElab = $dateElab;

        return $this;
    }

    public function getDateVal(): ?\DateTimeInterface
    {
        return $this->dateVal;
    }

    public function setDateVal(?\DateTimeInterface $dateVal): self
    {
        $this->dateVal = $dateVal;

        return $this;
    }

    public function isValider(): ?bool
    {
        return $this->valider;
    }

    public function setValider(?bool $valider): self
    {
        $this->valider = $valider;

        return $this;
    }

    public function isActiver(): ?bool
    {
        return $this->activer;
    }

    public function setActiver(?bool $activer): self
    {
        $this->activer = $activer;

        return $this;
    }

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(?string $periode): self
    {
        $this->periode = $periode;

        return $this;
    }

    public function getPersonne(): ?Personnel
    {
        return $this->personne;
    }

    public function setPersonne(?Personnel $personne): self
    {
        $this->personne = $personne;

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

    /**
     * @return Collection<int, ProgrammeElementProjet>
     */
    public function getProgrammeElementProjets(): Collection
    {
        return $this->programmeElementProjets;
    }

    public function addProgrammeElementProjet(ProgrammeElementProjet $programmeElementProjet): self
    {
        if (!$this->programmeElementProjets->contains($programmeElementProjet)) {
            $this->programmeElementProjets->add($programmeElementProjet);
            $programmeElementProjet->setProgramme($this);
        }

        return $this;
    }

    public function removeProgrammeElementProjet(ProgrammeElementProjet $programmeElementProjet): self
    {
        if ($this->programmeElementProjets->removeElement($programmeElementProjet)) {
            // set the owning side to null (unless already changed)
            if ($programmeElementProjet->getProgramme() === $this) {
                $programmeElementProjet->setProgramme(null);
            }
        }

        return $this;
    }

    public function getArticlePe(): ?ArticlePE
    {
        return $this->articlePe;
    }

    public function setArticlePe(?ArticlePE $articlePe): self
    {
        $this->articlePe = $articlePe;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNumpaiementprojet(): ?int
    {
        return $this->numpaiementprojet;
    }

    public function setNumpaiementprojet(?int $numpaiementprojet): self
    {
        $this->numpaiementprojet = $numpaiementprojet;

        return $this;
    }
}
