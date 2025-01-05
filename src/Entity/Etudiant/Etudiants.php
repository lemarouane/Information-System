<?php

namespace App\Entity\Etudiant;

use App\Entity\EtuHistoDemandes;
use App\Entity\Etudiant\image;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\EtudiantsRepository;


#[ORM\Entity(repositoryClass: EtudiantsRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\Table(name: 'etudiants')] 

class Etudiants implements UserInterface,\Serializable, PasswordAuthenticatedUserInterface 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column] 
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 10)]
    private ?string $locale = null;

    #[ORM\Column(length: 255)]
    private ?string $nomUtilisateur = null;


    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $code = null;
 
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $carte = null;

    #[ORM\Column(length: 255, nullable: true)] 
    private ?string $type = null;

    #[ORM\OneToOne(targetEntity: image::class, cascade: ['persist', 'remove'])]
    private $image;

    #[ORM\OneToMany(mappedBy: 'codeEtudiant', targetEntity: EtuAttestation::class)]
    private Collection $etuAttestations;

    #[ORM\OneToMany(mappedBy: 'codeEtudiant', targetEntity: EtuReleveAttestation::class)]
    private Collection $etuReleveAttestations;

    #[ORM\OneToMany(mappedBy: 'codeEtudiant', targetEntity: EtuDiplomeCarte::class)]
    private Collection $etuDiplomeCartes;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?ChoixOrientation $choix = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'],fetch:'EAGER')]
    private ?Etat $etat = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Stage::class)]
    private Collection $stages;

    #[ORM\OneToMany(mappedBy: 'idUser', cascade: ['persist', 'remove'], targetEntity: Reinscription::class)]
    private Collection $reinscriptions;

    #[ORM\OneToMany(mappedBy: 'idUser', targetEntity: Laureats::class)]
    private Collection $laureats;

    #[ORM\Column]
    private ?bool $enable = null;

    #[ORM\OneToMany(mappedBy: 'etudiants', targetEntity: EtudiantDD::class)]
    private Collection $etudiantDD;

    #[ORM\OneToMany(mappedBy: 'idUser', targetEntity: Absence::class)]
    private Collection $absences;




    public function __construct()
    {
        $this->etuAttestations = new ArrayCollection();
        $this->etuReleveAttestations = new ArrayCollection();
        $this->etuDiplomeCartes = new ArrayCollection();
        $this->stages = new ArrayCollection();
        $this->reinscriptions = new ArrayCollection();
        $this->laureats = new ArrayCollection();
        $this->etudiantDD = new ArrayCollection();
        $this->absences = new ArrayCollection();
        $this->image= new Image();

    }
 

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }



    public function getNomUtilisateur(): ?string
    {
        return $this->nomUtilisateur;
    }

    public function setNomUtilisateur(string $nomUtilisateur): self
    {
        $this->nomUtilisateur = $nomUtilisateur;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }


    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getImage(): ?image
    {
        return $this->image;
    }

    public function setImage(?image $image): self
    {
        $this->image = $image;

        return $this;
    }


    
    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($serialized);
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

    public function getCarte(): ?string
    {
        return $this->carte;
    }

    public function setCarte(?string $carte): self
    {
        $this->carte = $carte;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, EtuAttestation>
     */
    public function getEtuAttestations(): Collection
    {
        return $this->etuAttestations;
    }

    public function addEtuAttestation(EtuAttestation $etuAttestation): self
    {
        if (!$this->etuAttestations->contains($etuAttestation)) {
            $this->etuAttestations->add($etuAttestation);
            $etuAttestation->setCodeEtudiant($this);
        }

        return $this;
    }

    public function removeEtuAttestation(EtuAttestation $etuAttestation): self
    {
        if ($this->etuAttestations->removeElement($etuAttestation)) {
            // set the owning side to null (unless already changed)
            if ($etuAttestation->getCodeEtudiant() === $this) {
                $etuAttestation->setCodeEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EtuReleveAttestation>
     */
    public function getEtuReleveAttestations(): Collection
    {
        return $this->etuReleveAttestations;
    }

    public function addEtuReleveAttestation(EtuReleveAttestation $etuReleveAttestation): self
    {
        if (!$this->etuReleveAttestations->contains($etuReleveAttestation)) {
            $this->etuReleveAttestations->add($etuReleveAttestation);
            $etuReleveAttestation->setCodeEtudiant($this);
        }

        return $this;
    }

    public function removeEtuReleveAttestation(EtuReleveAttestation $etuReleveAttestation): self
    {
        if ($this->etuReleveAttestations->removeElement($etuReleveAttestation)) {
            // set the owning side to null (unless already changed)
            if ($etuReleveAttestation->getCodeEtudiant() === $this) {
                $etuReleveAttestation->setCodeEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EtuDiplomeCarte>
     */
    public function getEtuDiplomeCartes(): Collection
    {
        return $this->etuDiplomeCartes;
    }

    public function addEtuDiplomeCarte(EtuDiplomeCarte $etuDiplomeCarte): self
    {
        if (!$this->etuDiplomeCartes->contains($etuDiplomeCarte)) {
            $this->etuDiplomeCartes->add($etuDiplomeCarte);
            $etuDiplomeCarte->setCodeEtudiant($this);
        }

        return $this;
    }

    public function removeEtuDiplomeCarte(EtuDiplomeCarte $etuDiplomeCarte): self
    {
        if ($this->etuDiplomeCartes->removeElement($etuDiplomeCarte)) {
            // set the owning side to null (unless already changed)
            if ($etuDiplomeCarte->getCodeEtudiant() === $this) {
                $etuDiplomeCarte->setCodeEtudiant(null);
            }
        }

        return $this;
    }

    public function getChoix(): ?ChoixOrientation
    {
        return $this->choix;
    }

    public function setChoix(ChoixOrientation $choix): self
    {
        // set the owning side of the relation if necessary
        if ($choix->getUser() !== $this) {
            $choix->setUser($this);
        }

        $this->choix = $choix;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        // unset the owning side of the relation if necessary
        if ($etat === null && $this->etat !== null) {
            $this->etat->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($etat !== null && $etat->getUser() !== $this) {
            $etat->setUser($this);
        }

        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    public function addStage(Stage $stage): self
    {
        if (!$this->stages->contains($stage)) {
            $this->stages->add($stage);
            $stage->setUser($this);
        }

        return $this;
    }

    public function removeStage(Stage $stage): self
    {
        if ($this->stages->removeElement($stage)) {
            // set the owning side to null (unless already changed)
            if ($stage->getUser() === $this) {
                $stage->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reinscription>
     */
    public function getReinscriptions(): Collection
    {
        return $this->reinscriptions;
    }

    public function addReinscription(Reinscription $reinscription): self
    {
        if (!$this->reinscriptions->contains($reinscription)) {
            $this->reinscriptions->add($reinscription);
            $reinscription->setIdUser($this);
        }

        return $this;
    }

    public function removeReinscription(Reinscription $reinscription): self
    {
        if ($this->reinscriptions->removeElement($reinscription)) {
            // set the owning side to null (unless already changed)
            if ($reinscription->getIdUser() === $this) {
                $reinscription->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Laureats>
     */
    public function getLaureats(): Collection
    {
        return $this->laureats;
    }

    public function addLaureat(Laureats $laureat): self
    {
        if (!$this->laureats->contains($laureat)) {
            $this->laureats->add($laureat);
            $laureat->setIdUser($this);
        }

        return $this;
    }

    public function removeLaureat(Laureats $laureat): self
    {
        if ($this->laureats->removeElement($laureat)) {
            // set the owning side to null (unless already changed)
            if ($laureat->getIdUser() === $this) {
                $laureat->setIdUser(null);
            }
        }

        return $this;
    }

    public function isEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): self
    {
        $this->enable = $enable;

        return $this;
    }
    
    /**
     * @return Collection<int, EtudiantDD>
     */
    public function getEtudiantDD(): Collection
    {
        return $this->etudiantDD;
    }

    public function addEtudiantDD(EtudiantDD $etudiantDD): self
    {
        if (!$this->etudiantDD->contains($etudiantDD)) {
            $this->etudiantDD->add($etudiantDD);
            $etudiantDD->setEtudiants($this);
        }

        return $this;
    }

    public function removeEtudiantDD(EtudiantDD $etudiantDD): self
    {
        if ($this->etudiantDD->removeElement($etudiantDD)) {
            // set the owning side to null (unless already changed)
            if ($etudiantDD->getEtudiants() === $this) {
                $etudiantDD->setEtudiants(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Absence>
     */
    public function getAbsences(): Collection
    {
        return $this->absences;
    }

    public function addAbsence(Absence $absence): self
    {
        if (!$this->absences->contains($absence)) {
            $this->absences->add($absence);
            $absence->setIdUser($this);
        }

        return $this;
    }

    public function removeAbsence(Absence $absence): self
    {
        if ($this->absences->removeElement($absence)) {
            // set the owning side to null (unless already changed)
            if ($absence->getIdUser() === $this) {
                $absence->setIdUser(null);
            }
        }

        return $this;
    }
    

   

}
