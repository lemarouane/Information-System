<?php

namespace App\Entity\Etudiant;

use App\Repository\EtudiantDDRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtudiantDDRepository::class)]
#[ORM\Table(name: 'etudiantdd')] 
class EtudiantDD
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 255)]
    private ?string $anneeSoutenance = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $filiere = null;

    #[ORM\ManyToOne(inversedBy: 'etudiantDDs', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConventionDD $convention = null;

    #[ORM\ManyToOne(inversedBy: 'etudiantDD')]
    private ?Etudiants $etudiants = null;

    #[ORM\OneToMany(mappedBy: 'inscription', targetEntity: InscritEtudiant::class, orphanRemoval: true)]
    private Collection $inscritEtudiants;

    public function __construct()
    {
        $this->inscritEtudiants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getAnneeSoutenance(): ?string
    {
        return $this->anneeSoutenance;
    }

    public function setAnneeSoutenance(string $anneeSoutenance): self
    {
        $this->anneeSoutenance = $anneeSoutenance;

        return $this;
    }

    public function getFiliere(): ?string
    {
        return $this->filiere;
    }

    public function setFiliere(?string $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }

    public function getConvention(): ?ConventionDD
    {
        return $this->convention;
    }

    public function setConvention(?ConventionDD $convention): self
    {
        $this->convention = $convention;

        return $this;
    }

    public function getEtudiants(): ?Etudiants
    {
        return $this->etudiants;
    }

    public function setEtudiants(?Etudiants $etudiants): self
    {
        $this->etudiants = $etudiants;

        return $this;
    }

    /**
     * @return Collection<int, InscritEtudiant>
     */
    public function getInscritEtudiants(): Collection
    {
        return $this->inscritEtudiants;
    }

    public function addInscritEtudiant(InscritEtudiant $inscritEtudiant): self
    {
        if (!$this->inscritEtudiants->contains($inscritEtudiant)) {
            $this->inscritEtudiants->add($inscritEtudiant);
            $inscritEtudiant->setInscription($this);
        }

        return $this;
    }

    public function removeInscritEtudiant(InscritEtudiant $inscritEtudiant): self
    {
        if ($this->inscritEtudiants->removeElement($inscritEtudiant)) {
            // set the owning side to null (unless already changed)
            if ($inscritEtudiant->getInscription() === $this) {
                $inscritEtudiant->setInscription(null);
            }
        }

        return $this;
    }
    
}
