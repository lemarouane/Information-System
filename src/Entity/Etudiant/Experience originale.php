<?php

namespace App\Entity\Etudiant;

use App\Repository\ExperienceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
#[ORM\Table(name: 'experience originale')] 
class Experience_originale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $paysVille = null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $organisme = null;

    #[ORM\Column(length: 255)]
    private ?string $poste = null;

    #[ORM\Column(length: 255)]
    private ?string $departement = null;

    #[ORM\Column(length: 40)]
    private ?string $rhPhone = null;

    #[ORM\Column(length: 40)]
    private ?string $rhEmail = null;

    #[ORM\Column(length: 80)]
    private ?string $rhContact = null;

    #[ORM\ManyToOne(inversedBy: 'experiences', cascade:['persist'])]
    private ?Laureats $laureat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getPaysVille(): ?string
    {
        return $this->paysVille;
    }

    public function setPaysVille(?string $paysVille): self
    {
        $this->paysVille = $paysVille;

        return $this;
    }

    public function getOrganisme(): ?string
    {
        return $this->organisme;
    }

    public function setOrganisme(?string $organisme): self
    {
        $this->organisme = $organisme;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getRhPhone(): ?string
    {
        return $this->rhPhone;
    }

    public function setRhPhone(string $rhPhone): self
    {
        $this->rhPhone = $rhPhone;

        return $this;
    }

    public function getRhEmail(): ?string
    {
        return $this->rhEmail;
    }

    public function setRhEmail(string $rhEmail): self
    {
        $this->rhEmail = $rhEmail;

        return $this;
    }

    public function getRhContact(): ?string
    {
        return $this->rhContact;
    }

    public function setRhContact(string $rhContact): self
    {
        $this->rhContact = $rhContact;

        return $this;
    }

    public function getLaureat(): ?Laureats
    {
        return $this->laureat;
    }

    public function setLaureat(?Laureats $laureat): self
    {
        $this->laureat = $laureat;

        return $this;
    }
}
