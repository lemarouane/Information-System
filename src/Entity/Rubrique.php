<?php

namespace App\Entity;

use App\Repository\RubriqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RubriqueRepository::class)]
#[ORM\Table(name: 'rubrique')]
class Rubrique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(name: 'numRubrique')]
    private ?int $numRubrique = null;

    #[ORM\Column(length: 40)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'rubrique')]
    private ?Ligne $ligne = null;

    #[ORM\ManyToOne(inversedBy: 'rubriques')]
    private ?Paragraphe $paragraphe = null;

    #[ORM\ManyToOne(inversedBy: 'rubriques')]
    #[ORM\JoinColumn(name: 'articlePE_id')]
    private ?ArticlePE $articlePE = null;

    #[ORM\Column(length: 10)]
    private ?string $affichage = null;


///////////////////////////////
    #[ORM\OneToMany(mappedBy: 'rubrique', targetEntity: ProgrammeElementProjet::class)]
    private Collection $programmeElementProjets;

    #[ORM\OneToMany(mappedBy: 'rubrique', targetEntity: ProgrammeElementRestant::class)]
    private Collection $programmeElementRestants;

    #[ORM\OneToMany(mappedBy: 'rubrique', targetEntity: ExecutionElement::class)]
    private Collection $executionElements;

    #[ORM\OneToMany(mappedBy: 'rubrique' , targetEntity: ProgrammeElement::class)]
    private Collection $element;

    #[ORM\OneToMany(mappedBy: 'rubrique' , targetEntity: ProgrammeElementBudget::class)] 
    private Collection $elementBudget;

    public function __construct()
    {
        $this->programmeElementProjets = new ArrayCollection();
        $this->programmeElementRestants = new ArrayCollection();
        $this->executionElements = new ArrayCollection();
        $this->element = new ArrayCollection();
        $this->elementBudget = new ArrayCollection();
    }

////////////////////////////////////////

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getNumRubrique(): ?int
    {
        return $this->numRubrique;
    }

    public function setNumRubrique(int $numRubrique): self
    {
        $this->numRubrique = $numRubrique;

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

    public function getLigne(): ?Ligne
    {
        return $this->ligne;
    }

    public function setLigne(?Ligne $ligne): self
    {
        $this->ligne = $ligne;

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

    public function getArticlePE(): ?ArticlePE
    {
        return $this->articlePE;
    }

    public function setArticlePE(?ArticlePE $articlePE): self
    {
        $this->articlePE = $articlePE;

        return $this;
    }

    public function getAffichage(): ?string
    {
        return $this->affichage;
    }

    public function setAffichage(string $affichage): self
    {
        $this->affichage = $affichage;

        return $this;
    }

////////////////////////////////////////////////////

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
            $programmeElementProjet->setRubrique($this);
        }

        return $this;
    }

    public function removeProgrammeElementProjet(ProgrammeElementProjet $programmeElementProjet): self
    {
        if ($this->programmeElementProjets->removeElement($programmeElementProjet)) {
            // set the owning side to null (unless already changed)
            if ($programmeElementProjet->getRubrique() === $this) {
                $programmeElementProjet->setRubrique(null);
            }
        }

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
            $programmeElementRestant->setRubrique($this);
        }

        return $this;
    }

    public function removeProgrammeElementRestant(ProgrammeElementRestant $programmeElementRestant): self
    {
        if ($this->programmeElementRestants->removeElement($programmeElementRestant)) {
            // set the owning side to null (unless already changed)
            if ($programmeElementRestant->getRubrique() === $this) {
                $programmeElementRestant->setRubrique(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExecutionElement>
     */
    public function getExecutionElements(): Collection
    {
        return $this->executionElements;
    }

    public function addExecutionElement(ExecutionElement $executionElement): self
    {
        if (!$this->executionElements->contains($executionElement)) {
            $this->executionElements->add($executionElement);
            $executionElement->setRubrique($this);
        }

        return $this;
    }

    public function removeExecutionElement(ExecutionElement $executionElement): self
    {
        if ($this->executionElements->removeElement($executionElement)) {
            // set the owning side to null (unless already changed)
            if ($executionElement->getRubrique() === $this) {
                $executionElement->setRubrique(null);
            }
        }

        return $this;
    }

 

     /**
     * @return Collection<int, ProgrammeElement>
     */
    public function getElements(): Collection
    {
        return $this->element;
    }

    public function addElement(ProgrammeElement $element): self
    {
        if (!$this->element->contains($element)) {
            $this->element->add($element);
            $element->setRubrique($this);
        }

        return $this;
    }

    public function removeElement( $element): self
    {
        if ($this->element->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getRubrique() === $this) {
                $element->setRubrique(null);
            }
        }

        return $this;
    }

/////////////////////////////////////////////////////////////////////////////


   /**
     * @return Collection<int, ProgrammeElementBudget>
     */
    public function getElementsBudget(): Collection
    {
        return $this->elementBudget;
    }

    public function addElementBudget(ProgrammeElementBudget $elementBudget): self
    {
        if (!$this->elementBudget->contains($elementBudget)) {
            $this->elementBudget->add($elementBudget);
            $elementBudget->setRubrique($this);
        }

        return $this;
    }

    public function removeElementBudget( $elementBudget): self
    {
        if ($this->elementBudget->removeElement($elementBudget)) {
            // set the owning side to null (unless already changed)
            if ($elementBudget->getRubrique() === $this) {
                $elementBudget->setRubrique(null);
            }
        }

        return $this;
    }





}
