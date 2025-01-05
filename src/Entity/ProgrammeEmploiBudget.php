<?php

namespace App\Entity;

use App\Repository\ProgrammeEmploiBudgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeEmploiBudgetRepository::class)]
class ProgrammeEmploiBudget
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

    #[ORM\ManyToOne(inversedBy: 'programme')]
    private ?Personnel $personne = null; 

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCRE = null;

    #[ORM\Column(nullable: true)]
    private ?float $montantReste = null;

    #[ORM\Column]
    private ?bool $activer = null;

    #[ORM\OneToMany(mappedBy: 'programme', targetEntity: ProgrammeElementBudget::class  , cascade:["persist","remove"])]
    private Collection $element;

    #[ORM\ManyToOne(inversedBy: 'programmeEmploisBudget')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ArticlePE $articlePE = null;

    #[ORM\OneToMany(mappedBy: 'programmeBudget', targetEntity: ExecutionPE::class)]
    private Collection $executionPEBs;

    #[ORM\Column(nullable: true)]
    private ?bool $valider = null;

 

    

    public function __construct()
    {
        $this->element = new ArrayCollection();
        $this->executionPEBs = new ArrayCollection();
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



    public function getMontantReste(): ?float
    {
        return $this->montantReste;
    }

    public function setMontantReste(?float $montantReste): self
    {
        $this->montantReste = $montantReste;

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
     * @return Collection<int, ProgrammeElementBudget>
     */
    public function getElement(): Collection
    {
        return $this->element;
    }

    public function addElement(ProgrammeElementBudget $element): self
    {
        if (!$this->element->contains($element)) {
            $this->element->add($element);
            $element->setProgramme($this);
        }

        return $this;
    }

    public function removeElement(ProgrammeElementBudget $element): self
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

    /**
     * @return Collection<int, ExecutionPE>
     */
    public function getExecutionPEBs(): Collection
    {
        return $this->executionPEBs;
    }

    public function addExecutionPEB(ExecutionPE $executionPEB): self
    {
        if (!$this->executionPEBs->contains($executionPEB)) {
            $this->executionPEBs->add($executionPEB);
            $executionPEB->setProgrammeBudget($this);
        }

        return $this;
    }

    public function removeExecutionPEB(ExecutionPE $executionPEB): self
    {
        if ($this->executionPEBs->removeElement($executionPEB)) {
            // set the owning side to null (unless already changed)
            if ($executionPEB->getProgrammeBudget() === $this) {
                $executionPEB->setProgrammeBudget(null);
            }
        }

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
 
   
}
