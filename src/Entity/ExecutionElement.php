<?php

namespace App\Entity;

use App\Repository\ExecutionElementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExecutionElementRepository::class)]
class ExecutionElement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

   /*  #[ORM\ManyToOne(inversedBy: 'elementEX', cascade:["persist"])]
    private ?Rubrique $rubrique = null;

    #[ORM\ManyToOne(inversedBy: 'element', cascade:["persist"])]
    private ?ExecutionPE $execution = null; */


    #[ORM\Column(length: 255,nullable: true)]
    private ?string $intitule = null;

    #[ORM\Column(length: 255,nullable: true)]
    private ?string $description = null;
    
    #[ORM\Column(nullable: true)]
    private ?float $montant = null;

    #[ORM\ManyToOne(inversedBy: 'executionElements', cascade:["persist"])]
    private ?Rubrique $rubrique = null;

    #[ORM\ManyToOne(inversedBy: 'executionElements', cascade:["persist"])]
    private ?ExecutionPE $execution = null;

  


    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    // public function getRubrique(): ?Rubrique
    // {
    //     return $this->rubrique;
    // }

    // public function setRubrique(?Rubrique $rubrique): self
    // {
    //     $this->rubrique = $rubrique;

    //     return $this;
    // }

    // public function getExecution(): ?ExecutionPE
    // {
    //     return $this->execution;
    // }

    // public function setExecution(?ExecutionPE $execution): self
    // {
    //     $this->execution = $execution;

    //     return $this;
    // }

    public function getRubrique(): ?Rubrique
    {
        return $this->rubrique;
    }

    public function setRubrique(?Rubrique $rubrique): self
    {
        $this->rubrique = $rubrique;

        return $this;
    }

    public function getExecution(): ?ExecutionPE
    {
        return $this->execution;
    }

    public function setExecution(?ExecutionPE $execution): self
    {
        $this->execution = $execution;

        return $this;
    }
}
