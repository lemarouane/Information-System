<?php

namespace App\Entity\Etudiant;

use App\Repository\ConventionDDRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConventionDDRepository::class)]
#[ORM\Table(name: 'conventiondd')] 
class ConventionDD
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $etablissement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datedebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datefin = null;

    #[ORM\Column(length: 255)]
    private ?string $contactEnsa = null;

    #[ORM\Column(length: 255)]
    private ?string $contactEtab = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pays = null;

    #[ORM\Column]
    private array $filiere = [];

    #[ORM\OneToMany(mappedBy: 'convention', cascade: ['persist', 'remove'], targetEntity: EtudiantDD::class)]
    private Collection $etudiantDDs;

 

    public function __construct()
    {
        $this->etudiantDDs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtablissement(): ?string
    {
        return $this->etablissement;
    }

    public function setEtablissement(string $etablissement): self
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(?\DateTimeInterface $detadebut): self
    {
        $this->datedebut = $detadebut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(?\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getContactEnsa(): ?string
    {
        return $this->contactEnsa;
    }

    public function setContactEnsa(string $contactEnsa): self
    {
        $this->contactEnsa = $contactEnsa;

        return $this;
    }

    public function getContactEtab(): ?string
    {
        return $this->contactEtab;
    }

    public function setContactEtab(string $contactEtab): self
    {
        $this->contactEtab = $contactEtab;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getFiliere(): array
    {
        return $this->filiere;
    }

    public function setFiliere(?array $filiere): self
    {
        $this->filiere = $filiere;

        return $this;
    }

    /**
     * @return Collection<int, EtudiantDD>
     */
    public function getEtudiantDDs(): Collection
    {
        return $this->etudiantDDs;
    }

    public function addEtudiantDD(EtudiantDD $etudiantDD): self
    {
        if (!$this->etudiantDDs->contains($etudiantDD)) {
            $this->etudiantDDs->add($etudiantDD);
            $etudiantDD->setConvention($this);
        }

        return $this;
    }

    public function removeEtudiantDD(EtudiantDD $etudiantDD): self
    {
        if ($this->etudiantDDs->removeElement($etudiantDD)) {
            // set the owning side to null (unless already changed)
            if ($etudiantDD->getConvention() === $this) {
                $etudiantDD->setConvention(null);
            }
        }

        return $this;
    }

}
