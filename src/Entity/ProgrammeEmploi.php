<?php

namespace App\Entity;

use App\Repository\ProgrammeEmploiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeEmploiRepository::class)]
class ProgrammeEmploi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    #[ORM\Column(length: 255)]
    private ?string $annee = null;

    #[ORM\Column]
    private ?float $montant = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'programme')]
    private ?Personnel $personne = null; 

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCRE = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateELAB = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateVAL = null;

    #[ORM\Column(nullable: true)]
    private ?float $montantReste = null;

    #[ORM\Column]
    private ?bool $valider = null;

    #[ORM\Column]
    private ?bool $activer = null;

    #[ORM\OneToMany(mappedBy: 'programme', targetEntity: ProgrammeElement::class  , cascade:["persist","remove"])]
    private Collection $element;

    #[ORM\ManyToOne(inversedBy: 'programmeEmplois')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ArticlePE $articlePE = null;

    #[ORM\ManyToOne(inversedBy: 'programmeEmplois')]
    private ?Paragraphe $paragraphe = null;

    #[ORM\Column(nullable: true)]
    private ?int $anneeuniv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeapofc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $periode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien2 = null;
    ////////////////////
    #[ORM\OneToMany(mappedBy: 'programme', targetEntity: ExecutionPE::class)]
    private Collection $executionPEs;

    ///////////////////////

    

    public function __construct()
    {
        $this->element = new ArrayCollection();

        ///////////////
        $this->executionPEs = new ArrayCollection();
        ////////////////////
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

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

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getDateCRE(): ?\DateTimeInterface
    {
        return $this->dateCRE;
    }

    public function setDateCRE(\DateTimeInterface $dateCRE): self
    {
        $this->dateCRE = $dateCRE;

        return $this;
    }

    public function getDateELAB(): ?\DateTimeInterface
    {
        return $this->dateELAB;
    }

    public function setDateELAB(?\DateTimeInterface $dateELAB): self
    {
        $this->dateELAB = $dateELAB;

        return $this;
    }

    public function getDateVAL(): ?\DateTimeInterface
    {
        return $this->dateVAL;
    }

    public function setDateVAL(?\DateTimeInterface $dateVAL): self
    {
        $this->dateVAL = $dateVAL;

        return $this;
    }

    public function getMontantReste(): ?float
    {
        return $this->montantReste;
    }

    public function setMontantReste(?float $montantReste): self
    {
        $this->montantReste = $montantReste;

        return $this;
    }

    public function isValider(): ?bool
    {
        return $this->valider;
    }

    public function setValider(bool $valider): self
    {
        $this->valider = $valider;

        return $this;
    }

    public function isActiver(): ?bool
    {
        return $this->activer;
    }

    public function setActiver(bool $activer): self
    {
        $this->activer = $activer;

        return $this;
    }

    /**
     * @return Collection<int, ProgrammeElement>
     */
    public function getElement(): Collection
    {
        return $this->element;
    }

    public function addElement(ProgrammeElement $element): self
    {
        if (!$this->element->contains($element)) {
            $this->element->add($element);
            $element->setProgramme($this);
        }

        return $this;
    }

    public function removeElement(ProgrammeElement $element): self
    {
        if ($this->element->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getProgramme() === $this) {
                $element->setProgramme(null);
            }
        }

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

    public function getParagraphe(): ?Paragraphe
    {
        return $this->paragraphe;
    }

    public function setParagraphe(?Paragraphe $paragraphe): self
    {
        $this->paragraphe = $paragraphe;

        return $this;
    }

    public function getAnneeuniv(): ?int
    {
        return $this->anneeuniv;
    }

    public function setAnneeuniv(?int $anneeuniv): self
    {
        $this->anneeuniv = $anneeuniv;

        return $this;
    }

    public function getCodeapofc(): ?string
    {
        return $this->codeapofc;
    }

    public function setCodeapofc(?string $codeapofc): self
    {
        $this->codeapofc = $codeapofc;

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


  public function getLien1(): ?string
    {
        return $this->lien1;
    }

    public function setLien1(?string $lien1): self
    {
        $this->lien1 = $lien1;

        return $this;
    }

    public function getLien2(): ?string
    {
        return $this->lien2;
    }

    public function setLien2(?string $lien2): self
    {
        $this->lien2 = $lien2;

        return $this;
    }

 /////////////////////////////////////////////////////
 
    /**
     * @return Collection<int, ExecutionPE>
     */
    public function getExecutionPEs(): Collection
    {
        return $this->executionPEs;
    }

    public function addExecutionPE(ExecutionPE $executionPE): self
    {
        if (!$this->executionPEs->contains($executionPE)) {
            $this->executionPEs->add($executionPE);
            $executionPE->setProgramme($this);
        }

        return $this;
    }

    public function removeExecutionPE(ExecutionPE $executionPE): self
    {
        if ($this->executionPEs->removeElement($executionPE)) {
            // set the owning side to null (unless already changed)
            if ($executionPE->getProgramme() === $this) {
                $executionPE->setProgramme(null);
            }
        }

        return $this;
    }
///////////////////////////////////////////////////

   
}
