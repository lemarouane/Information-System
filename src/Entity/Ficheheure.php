<?php

namespace App\Entity;

use App\Repository\FicheheureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheheureRepository::class)]
class Ficheheure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'ficheheures')]
    private ?Personnel $personnel = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etablissement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nbHeure = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $moisDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $moisFin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnvoie = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emploi = null;

    #[ORM\Column(nullable: true)]
    private ?bool $bloque = null;

    #[ORM\OneToMany(mappedBy: 'ficheheure', targetEntity: Engagementheure::class , cascade:["persist","remove"])]
    private Collection $engagements;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $niveau = null;

    public function __construct()
    {
        $this->engagements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEtablissement(): ?string
    {
        return $this->etablissement;
    }

    public function setEtablissement(?string $etablissement): self
    {
        $this->etablissement = $etablissement;

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

    public function getNbHeure(): ?string
    {
        return $this->nbHeure;
    }

    public function setNbHeure(?string $nbHeure): self
    {
        $this->nbHeure = $nbHeure;

        return $this;
    }

    public function getMoisDebut(): ?\DateTimeInterface
    {
        return $this->moisDebut;
    }

    public function setMoisDebut(?\DateTimeInterface $moisDebut): self
    {
        $this->moisDebut = $moisDebut;

        return $this;
    }

    public function getMoisFin(): ?\DateTimeInterface
    {
        return $this->moisFin;
    }

    public function setMoisFin(?\DateTimeInterface $moisFin): self
    {
        $this->moisFin = $moisFin;

        return $this;
    }

    public function getDateEnvoie(): ?\DateTimeInterface
    {
        return $this->dateEnvoie;
    }

    public function setDateEnvoie(?\DateTimeInterface $dateEnvoie): self
    {
        $this->dateEnvoie = $dateEnvoie;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
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

    public function getEmploi(): ?string
    {
        return $this->emploi;
    }

    public function setEmploi(?string $emploi): self
    {
        $this->emploi = $emploi;

        return $this;
    }

    public function isBloque(): ?bool
    {
        return $this->bloque;
    }

    public function setBloque(?bool $bloque): self
    {
        $this->bloque = $bloque;

        return $this;
    }

    /**
     * @return Collection<int, Engagementheure>
     */
    public function getengagements(): Collection
    {
        return $this->engagements;
    }

    public function addEngagement(Engagementheure $engagementheure): self
    {
        if (!$this->engagements->contains($engagementheure)) {
            $this->engagements->add($engagementheure);
            $engagementheure->setFicheheure($this);
        }

        return $this;
    }

    public function removeEngagement(Engagementheure $engagementheure): self
    {
        if ($this->engagements->removeElement($engagementheure)) {
            // set the owning side to null (unless already changed)
            if ($engagementheure->getFicheheure() === $this) {
                $engagementheure->setFicheheure(null);
            }
        }

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(?string $niveau): self
    {
        $this->niveau = $niveau;

        return $this;
    }
}
