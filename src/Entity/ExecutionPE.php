<?php

namespace App\Entity;

use App\Repository\ExecutionPERepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExecutionPERepository::class)]
class ExecutionPE
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

 /*    #[ORM\ManyToOne(inversedBy: 'execution', cascade:["persist"])]
    private ?ProgrammeEmploi $programme = null;

    #[ORM\OneToMany(mappedBy: 'execution', cascade:["persist","remove"])]
    private ?ExecutionElement $element = null;
     */


    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateCRE = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $motif = null;
    
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateMOD = null;

    #[ORM\OneToMany(mappedBy: 'execution', targetEntity: ExecutionElement::class, cascade:["persist","remove"])]
    private Collection $executionElements;

    #[ORM\ManyToOne(inversedBy: 'executionPEs', cascade:["persist"])]
    private ?ProgrammeEmploi $programme = null;

    #[ORM\ManyToOne(inversedBy: 'executionPEBs')]
    private ?ProgrammeEmploiBudget $programmeBudget = null;

 

    
    public function __construct()
    {
        $this->element = new ArrayCollection();
        $this->executionElements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getDateMOD(): ?\DateTimeInterface
    {
        return $this->dateMOD;
    }

    public function setDateMOD(?\DateTimeInterface $dateMOD): self
    {
        $this->dateMOD = $dateMOD;

        return $this;
    }


    

    // public function getProgramme(): ?ProgrammeEmploi
    // {
    //     return $this->programme;
    // }

    // public function setProgramme(?ProgrammeEmploi $programme): self
    // {
    //     $this->programme = $programme;

    //     return $this;
    // }

    // /**
    //  * @return Collection|ExecutionElement[]
    //  */
    // public function getElement(): Collection
    // {
    //     return $this->element;
    // }

    // public function addElement(ExecutionElement $element): self
    // {
    //     if (!$this->element->contains($element)) {
    //         $this->element[] = $element;
    //         $element->setExecution($this);
    //     }

    //     return $this;
    // }

    // public function removeElement(ExecutionElement $element): self
    // {
    //     if ($this->element->contains($element)) {
    //         $this->element->removeElement($element);
    //         // set the owning side to null (unless already changed)
    //         if ($element->getExecution() === $this) {
    //             $element->setExecution(null);
    //         }
    //     }

    //     return $this;
    // }

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
            $executionElement->setExecution($this);
        }

        return $this;
    }

    public function removeExecutionElement(ExecutionElement $executionElement): self
    {
        if ($this->executionElements->removeElement($executionElement)) {
            // set the owning side to null (unless already changed)
            if ($executionElement->getExecution() === $this) {
                $executionElement->setExecution(null);
            }
        }

        return $this;
    }

    public function getProgramme(): ?ProgrammeEmploi
    {
        return $this->programme;
    }

    public function setProgramme(?ProgrammeEmploi $programme): self
    {
        $this->programme = $programme;

        return $this;
    }

    public function getProgrammeBudget(): ?ProgrammeEmploiBudget
    {
        return $this->programmeBudget;
    }

    public function setProgrammeBudget(?ProgrammeEmploiBudget $programmeBudget): self
    {
        $this->programmeBudget = $programmeBudget;

        return $this;
    }
}
