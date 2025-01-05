<?php

namespace App\Entity\Etudiant;

use App\Repository\LaureatsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LaureatsRepository::class)]
#[ORM\Table(name: 'laureats')] 
class Laureats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $emailPerso = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailPro = null;

    #[ORM\Column(length: 40)]
    private ?string $mobile = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $domicile = null;

    #[ORM\Column(length: 255)]
    private ?string $adressePostale = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedin = null;

    #[ORM\Column(length: 255)]
    private ?string $situation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $disciplineDoc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cedDoc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $specialiteDoc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $universiteDoc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $autreSituation = null;

    #[ORM\ManyToOne(inversedBy: 'laureats' )]
    private ?Etudiants $idUser = null;

    #[ORM\Column(length: 4)]
    private ?string $delai = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paysVille = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $organisme = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $departement = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $rhPhone = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $rhEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $rhContact = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $poste = null;

    #[ORM\OneToMany(mappedBy: 'laureat', cascade:['persist', 'remove'], targetEntity: Experience::class)]
    private Collection $experiences;

    public function __construct()
    {
        $this->experiences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailPerso(): ?string
    {
        return $this->emailPerso;
    }

    public function setEmailPerso(string $emailPerso): self
    {
        $this->emailPerso = $emailPerso;

        return $this;
    }

    public function getEmailPro(): ?string
    {
        return $this->emailPro;
    }

    public function setEmailPro(?string $emailPro): self
    {
        $this->emailPro = $emailPro;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getDomicile(): ?string
    {
        return $this->domicile;
    }

    public function setDomicile(?string $domicile): self
    {
        $this->domicile = $domicile;

        return $this;
    }

    public function getAdressePostale(): ?string
    {
        return $this->adressePostale;
    }

    public function setAdressePostale(string $adressePostale): self
    {
        $this->adressePostale = $adressePostale;

        return $this;
    }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): self
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getSituation(): ?string
    {
        return $this->situation;
    }

    public function setSituation(string $situation): self
    {
        $this->situation = $situation;

        return $this;
    }

    public function getDisciplineDoc(): ?string
    {
        return $this->disciplineDoc;
    }

    public function setDisciplineDoc(?string $disciplineDoc): self
    {
        $this->disciplineDoc = $disciplineDoc;

        return $this;
    }

    public function getCedDoc(): ?string
    {
        return $this->cedDoc;
    }

    public function setCedDoc(?string $cedDoc): self
    {
        $this->cedDoc = $cedDoc;

        return $this;
    }

    public function getSpecialiteDoc(): ?string
    {
        return $this->specialiteDoc;
    }

    public function setSpecialiteDoc(?string $specialiteDoc): self
    {
        $this->specialiteDoc = $specialiteDoc;

        return $this;
    }

    public function getUniversiteDoc(): ?string
    {
        return $this->universiteDoc;
    }

    public function setUniversiteDoc(?string $universiteDoc): self
    {
        $this->universiteDoc = $universiteDoc;

        return $this;
    }

    public function getAutreSituation(): ?string
    {
        return $this->autreSituation;
    }

    public function setAutreSituation(?string $autreSituation): self
    {
        $this->autreSituation = $autreSituation;

        return $this;
    }

    public function getIdUser(): ?Etudiants
    {
        return $this->idUser;
    }

    public function setIdUser(?Etudiants $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getDelai(): ?string
    {
        return $this->delai;
    }

    public function setDelai(string $delai): self
    {
        $this->delai = $delai;

        return $this;
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

    public function getDepartement(): ?string
    {
        return $this->departement;
    }

    public function setDepartement(?string $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getRhPhone(): ?string
    {
        return $this->rhPhone;
    }

    public function setRhPhone(?string $rhPhone): self
    {
        $this->rhPhone = $rhPhone;

        return $this;
    }

    public function getRhEmail(): ?string
    {
        return $this->rhEmail;
    }

    public function setRhEmail(?string $rhEmail): self
    {
        $this->rhEmail = $rhEmail;

        return $this;
    }

    public function getRhContact(): ?string
    {
        return $this->rhContact;
    }

    public function setRhContact(?string $rhContact): self
    {
        $this->rhContact = $rhContact;

        return $this;
    }

    public function getPoste(): ?string
    {
        return $this->poste;
    }

    public function setPoste(?string $poste): self
    {
        $this->poste = $poste;

        return $this;
    }

    /**
     * @return Collection<int, Experience>
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experience $experience): self
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences->add($experience);
            $experience->setLaureat($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): self
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getLaureat() === $this) {
                $experience->setLaureat(null);
            }
        }

        return $this;
    }
}
