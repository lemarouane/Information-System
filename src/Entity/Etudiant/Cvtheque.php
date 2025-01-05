<?php

namespace App\Entity\Etudiant;

use App\Repository\CvthequeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\FormTypeInterface ;

#[ORM\Entity(repositoryClass: CvthequeRepository::class)]
#[ORM\Table(name: 'cvtheque')] 
class Cvtheque
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $emailPerso = null;


    #[ORM\Column(length: 40 , nullable: true)]
    private ?string $mobile = null;


    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $adressePostale = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedin = null;


    #[ORM\ManyToOne(inversedBy: 'cvtheque' )]
    private ?Etudiants $idUser = null; 


    #[ORM\OneToMany(mappedBy: 'cvtheque', cascade:['persist', 'remove'], targetEntity: Experience::class)]
    private Collection $experiences;

    #[ORM\OneToMany(mappedBy: 'cvtheque', cascade:['persist', 'remove'], targetEntity: Clubs::class)]
    private Collection $clubs;

    #[ORM\OneToMany(mappedBy: 'cvtheque', cascade:['persist', 'remove'], targetEntity: Formations::class)]
    private Collection $formations;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $permis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comp_technique = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $soft_skills = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $certifications = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $distinctions = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hobbies = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $langues = null;

    ///////////////////////////////////////////
    #[ORM\Column(length: 255 , nullable: true)]
    private ?string $situation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $emailPro = null;

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

    #[ORM\Column(length: 4 , nullable: true)]
    private ?string $delai = null;

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



    public function __construct()
    {
        $this->experiences = new ArrayCollection();
        $this->clubs = new ArrayCollection();
        $this->formations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

   

    public function getEmailPerso(): ?string
    {
        return $this->emailPerso;
    }

    public function setEmailPerso(?string $emailPerso): self
    {
        $this->emailPerso = $emailPerso;

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


    public function getIdUser(): ?Etudiants
    {
        return $this->idUser;
    }

    public function setIdUser(?Etudiants $idUser): self
    {
        $this->idUser = $idUser;

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
            $experience->setCvtheque($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): self
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getCvtheque() === $this) {
                $experience->setCvtheque(null);
            }
        }

        return $this;
    }


 /**
     * @return Collection<int, Clubs>
     */
    public function getClubs(): Collection
    {
        return $this->clubs;
    }

    public function addClubs(Clubs $clubs): self
    {
        if (!$this->clubs->contains($clubs)) {
            $this->clubs->add($clubs);
            $clubs->setCvtheque($this);
        }

        return $this;
    }

    public function removeClubs(Clubs $clubs): self
    {
        if ($this->clubs->removeElement($clubs)) {
            // set the owning side to null (unless already changed)
            if ($clubs->getCvthequet() === $this) {
                $clubs->setCvtheque(null);
            }
        }

        return $this;
    }



 /**
     * @return Collection<int, Formations>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }

    public function addFormations(Formations $formations): self
    {
        if (!$this->formations->contains($formations)) {
            $this->formations->add($formations);
            $formations->setCvtheque($this);
        }

        return $this;
    }

    public function removeFormations(Clubs $formations): self
    {
        if ($this->formations->removeElement($formations)) {
            // set the owning side to null (unless already changed)
            if ($formations->getCvtheque() === $this) {
                $formations->setCvtheque(null);
            }
        }

        return $this;
    }






    public function getPermis(): ?string
    {
        return $this->permis;
    }

    public function setPermis(?string $permis): self
    {
        $this->permis = $permis;

        return $this;
    }

    public function getCompTechnique(): ?string
    {
        return $this->comp_technique;
    }

    public function setCompTechnique(?string $comp_technique): self
    {
        $this->comp_technique = $comp_technique;

        return $this;
    }

    public function getSoftSkills(): ?string
    {
        return $this->soft_skills;
    }

    public function setSoftSkills(?string $soft_skills): self
    {
        $this->soft_skills = $soft_skills;

        return $this;
    }

    public function getCertifications(): ?string
    {
        return $this->certifications;
    }

    public function setCertifications(?string $certifications): self
    {
        $this->certifications = $certifications;

        return $this;
    }

    public function getDistinctions(): ?string
    {
        return $this->distinctions;
    }

    public function setDistinctions(?string $distinctions): self
    {
        $this->distinctions = $distinctions;

        return $this;
    }

    public function getHobbies(): ?string
    {
        return $this->hobbies;
    }

    public function setHobbies(?string $hobbies): self
    {
        $this->hobbies = $hobbies;

        return $this;
    }

    public function getLangues(): ?string
    {
        return $this->langues;
    }

    public function setLangues(?string $langues): self
    {
        $this->langues = $langues;

        return $this;
    }

    public function addClub(Clubs $club): self
    {
        if (!$this->clubs->contains($club)) {
            $this->clubs->add($club);
            $club->setCvtheque($this);
        }

        return $this;
    }

    public function removeClub(Clubs $club): self
    {
        if ($this->clubs->removeElement($club)) {
            // set the owning side to null (unless already changed)
            if ($club->getCvtheque() === $this) {
                $club->setCvtheque(null);
            }
        }

        return $this;
    }

    public function addFormation(Formations $formation): self
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setCvtheque($this);
        }

        return $this;
    }

    public function removeFormation(Formations $formation): self
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getCvtheque() === $this) {
                $formation->setCvtheque(null);
            }
        }

        return $this;
    }

    //////////////

    public function getEmailPro(): ?string
    {
        return $this->emailPro;
    }

    public function setEmailPro(?string $emailPro): self
    {
        $this->emailPro = $emailPro;

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

    public function getDelai(): ?string
    {
        return $this->delai;
    }

    public function setDelai(string $delai): self
    {
        $this->delai = $delai;

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

}
