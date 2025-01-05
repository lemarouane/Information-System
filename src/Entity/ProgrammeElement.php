<?php

namespace App\Entity;

use App\Repository\ProgrammeElementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgrammeElementRepository::class)]
class ProgrammeElement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'element')]
    private ?Rubrique $rubrique = null;

    #[ORM\ManyToOne(inversedBy: 'element')]
    private ?ProgrammeEmploi $programme = null;

    //////////////////////////////

    // #[ORM\OneToMany(mappedBy: 'progelement', targetEntity: Execution::class  , cascade:["persist","remove"])]
    // private Collection $executions;

    // public function __construct()
    // {
    //     $this->executions = new ArrayCollection(); 
    // }
  /////////////////////////////////////////////

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): self
    {
        $this->montant = $montant;

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

    public function getProgramme(): ?ProgrammeEmploi
    {
        return $this->programme;
    }

    public function setProgramme(?ProgrammeEmploi $programme): self
    {
        $this->programme = $programme;

        return $this;
    }

////////////////////////////////////////////////////////////
    //  /**
    //  * @return Collection<int, Execution>
    //  */
    // public function getExecutions(): Collection
    // {
    //     return $this->executions;
    // }

    // public function addExecution(Execution $execution): self
    // {
    //     if (!$this->executions->contains($execution)) {
    //         $this->executions->add($execution);
    //         $execution->setProgelement($this);
    //     }

    //     return $this;
    // }

    // public function removeExecution(Execution $execution): self
    // {
    //     if ($this->executions->removeElement($execution)) {
    //         // set the owning side to null (unless already changed)
    //         if ($execution->getProgelement() === $this) {
    //             $execution->setProgelement(null);
    //         }
    //     }

    //     return $this;
    // }

    //////////////////////////////////////////////////////
}
