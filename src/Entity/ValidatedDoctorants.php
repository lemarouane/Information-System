<?php

namespace App\Entity;

use App\Repository\ValidatedDoctorantsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValidatedDoctorantsRepository::class)]
#[ORM\Table(name: "validated_doctorants", schema: "pgi_ensa_db")]
class ValidatedDoctorants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Doctorants::class)]
    #[ORM\JoinColumn(name: "doctorant_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Doctorants $doctorant = null;

    #[ORM\ManyToOne(targetEntity: Personnel::class)]
    #[ORM\JoinColumn(name: "personnel_id", referencedColumnName: "id", nullable: false, onDelete: "CASCADE")]
    private ?Personnel $personnel = null;

    #[ORM\ManyToOne(targetEntity: StructRech::class)]
    #[ORM\JoinColumn(name: "structure_id", referencedColumnName: "id", nullable: true, onDelete: "SET NULL")]
    private ?StructRech $structure = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $cin = null;

    #[ORM\Column(length: 255)]
    private ?string $cne = null;

    #[ORM\Column(length: 255)]
    private ?string $choix = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDoctorant(): ?Doctorants
    {
        return $this->doctorant;
    }

    public function setDoctorant(?Doctorants $doctorant): self
    {
        $this->doctorant = $doctorant;

        return $this;
    }

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;

        return $this;
    }

    public function getStructure(): ?StructRech
    {
        return $this->structure;
    }

    public function setStructure(?StructRech $structure): self
    {
        $this->structure = $structure;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getCne(): ?string
    {
        return $this->cne;
    }

    public function setCne(string $cne): self
    {
        $this->cne = $cne;

        return $this;
    }

    public function getChoix(): ?string
    {
        return $this->choix;
    }

    public function setChoix(string $choix): self
    {
        $this->choix = $choix;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }
}
