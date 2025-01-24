<?php

namespace App\Entity;

use App\Repository\PersonnelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PersonnelRepository::class)]
#[Vich\Uploadable]
class Personnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $numPPR = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $genre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateRecrutement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAffectationMESRSFC = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAffectationEnseignement = null;


    #[Vich\UploadableField(mapping: 'personnel', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string',nullable : true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable : true)]
    private ?string $email = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etabDelivreDiplome = null;

    #[ORM\OneToOne(inversedBy: 'personnel', cascade: ['persist', 'remove'])]
    private ?Utilisateurs $idUser = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $tel = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?Departement $departementId = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?Corps $corpsId = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?Grades $gradeId = null;
 
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomArabe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenomArabe = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?TypePersonnel $typePersonnelId = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?SituationAdm $situationAdm = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?Province $provinceNaissance = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?Fonction $fonctionExercee = null;

    #[ORM\Column(nullable: true)]
    private ?int $soldeConge = null;

    #[ORM\Column(nullable: true)]
    private ?int $soldeCongeEx = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $cin = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $posteBudget = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbEnfant = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $situationFamiliale = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateAffectationENSAT = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?StructRech $structureRech = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateSoutenanceH = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datedeces = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Avancement::class)]
    #[ORM\OrderBy(["dateDeci" => "ASC","dateGrade"=>"ASC"])]
    private Collection $avancements;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: NoteFonctionnaire::class)]
    private Collection $noteFonctionnaires;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $activite = null;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: SoldeConge::class)]
    private Collection $soldeConges;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Document::class)]
    private Collection $documents;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Attestation::class)]
    private Collection $attestations;

    #[ORM\OneToMany(mappedBy: 'demandeur', targetEntity: HistoDemandes::class)]
    private Collection $histoDemandes;

    #[ORM\OneToMany(mappedBy: 'validateur', targetEntity: HistoDemandes::class)]
    private Collection $histoDemandes_v;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Autorisation::class)]
    private Collection $autorisations;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Conge::class)]
    private Collection $conges;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: OrdreMission::class)]
    private Collection $ordreMissions;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Engagement::class)]
    private Collection $engagements;

    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Ficheheure::class)]
    private Collection $ficheheures;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?Service $serviceAffectationId = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?Echelon $echelonId = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?Specialite $specialiteId = null;

    #[ORM\ManyToOne(inversedBy: 'personnels')]
    private ?Diplome $diplomeId = null;

    #[ORM\OneToMany(mappedBy: 'validateur', targetEntity: EtuHistoDemandes::class)]
    private Collection $etuHistoDemandes;

    #[ORM\OneToMany(mappedBy: 'responsable', targetEntity: Paiement::class)]
    private Collection $paiements;





    



    #[ORM\OneToMany(mappedBy: 'responsable', targetEntity: FiliereFcResponsable::class)]
    private Collection $filiereFcResponsables;


    #[ORM\OneToMany(mappedBy: 'responsable', targetEntity: Paiementprojet::class)]
    private Collection $paiementprojets;











    #[ORM\OneToMany(mappedBy: 'personnel', targetEntity: Publication::class, cascade: ['persist', 'remove'])]
    private Collection $publications;




    

    #[ORM\Column]
    private ?int $scopusId = null;

    public function __construct()
    {
        $this->avancements = new ArrayCollection();
        $this->noteFonctionnaires = new ArrayCollection();
        $this->soldeConges = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->attestations = new ArrayCollection();
        $this->histoDemandes = new ArrayCollection();
        $this->autorisations = new ArrayCollection();
        $this->histoDemandes_v = new ArrayCollection();
        $this->etuHistoDemandes = new ArrayCollection();
        $this->conges = new ArrayCollection();
        $this->ordreMissions = new ArrayCollection();
        $this->engagements = new ArrayCollection();
        $this->ficheheures = new ArrayCollection();
        $this->paiements = new ArrayCollection();
        $this->filiereFcResponsables = new ArrayCollection();
        $this->paiementprojets = new ArrayCollection();



        $this->publications = new ArrayCollection();
    }

   
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
   
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumPPR(): ?int
    {
        return $this->numPPR;
    }

    public function setNumPPR(?int $numPPR): self
    {
        $this->numPPR = $numPPR;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getDateRecrutement(): ?\DateTimeInterface
    {
        return $this->dateRecrutement;
    }

    public function setDateRecrutement(?\DateTimeInterface $dateRecrutement): self
    {
        $this->dateRecrutement = $dateRecrutement;

        return $this;
    }

    public function getDateAffectationMESRSFC(): ?\DateTimeInterface
    {
        return $this->dateAffectationMESRSFC;
    }

    public function setDateAffectationMESRSFC(?\DateTimeInterface $dateAffectationMESRSFC): self
    {
        $this->dateAffectationMESRSFC = $dateAffectationMESRSFC;

        return $this;
    }

    public function getDateAffectationEnseignement(): ?\DateTimeInterface
    {
        return $this->dateAffectationEnseignement;
    }

    public function setDateAffectationEnseignement(?\DateTimeInterface $dateAffectationEnseignement): self
    {
        $this->dateAffectationEnseignement = $dateAffectationEnseignement;

        return $this;
    }

    public function getEtabDelivreDiplome(): ?string
    {
        return $this->etabDelivreDiplome;
    }

    public function setEtabDelivreDiplome(?string $etabDelivreDiplome): self
    {
        $this->etabDelivreDiplome = $etabDelivreDiplome;

        return $this;
    }

    public function getIdUser(): ?Utilisateurs
    {
        return $this->idUser;
    }

    public function setIdUser(?Utilisateurs $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getDepartementId(): ?Departement
    {
        return $this->departementId;
    }

    public function setDepartementId(?Departement $departementId): self
    {
        $this->departementId = $departementId;

        return $this;
    }

    public function getCorpsId(): ?Corps
    {
        return $this->corpsId;
    }

    public function setCorpsId(?Corps $corpsId): self
    {
        $this->corpsId = $corpsId;

        return $this;
    }

    public function getGradeId(): ?Grades
    {
        return $this->gradeId;
    }

    public function setGradeId(?Grades $gradeId): self
    {
        $this->gradeId = $gradeId;

        return $this;
    }

    public function getNomArabe(): ?string
    {
        return $this->nomArabe;
    }

    public function setNomArabe(?string $nomArabe): self
    {
        $this->nomArabe = $nomArabe;

        return $this;
    }

    public function getPrenomArabe(): ?string
    {
        return $this->prenomArabe;
    }

    public function setPrenomArabe(?string $prenomArabe): self
    {
        $this->prenomArabe = $prenomArabe;

        return $this;
    }

    public function getTypePersonnelId(): ?TypePersonnel
    {
        return $this->typePersonnelId;
    }

    public function setTypePersonnelId(?TypePersonnel $typePersonnelId): self
    {
        $this->typePersonnelId = $typePersonnelId;

        return $this;
    }

    public function getSituationAdm(): ?SituationAdm
    {
        return $this->situationAdm;
    }

    public function setSituationAdm(?SituationAdm $situationAdm): self
    {
        $this->situationAdm = $situationAdm;

        return $this;
    }

    public function getProvinceNaissance(): ?Province
    {
        return $this->provinceNaissance;
    }

    public function setProvinceNaissance(?Province $provinceNaissance): self
    {
        $this->provinceNaissance = $provinceNaissance;

        return $this;
    }

    public function getDiplomeId(): ?Diplome
    {
        return $this->diplomeId;
    }

    public function setDiplomeId(?Diplome $diplomeId): self
    {
        $this->diplomeId = $diplomeId;

        return $this;
    }

    public function getSpecialiteId(): ?Specialite
    {
        return $this->specialiteId;
    }

    public function setSpecialiteId(?Specialite $specialiteId): self
    {
        $this->specialiteId = $specialiteId;

        return $this;
    }

    public function getFonctionExercee(): ?Fonction
    {
        return $this->fonctionExercee;
    }

    public function setFonctionExercee(?Fonction $fonctionExercee): self
    {
        $this->fonctionExercee = $fonctionExercee;

        return $this;
    }

    public function getSoldeConge(): ?int
    {
        return $this->soldeConge;
    }

    public function setSoldeConge(?int $soldeConge): self
    {
        $this->soldeConge = $soldeConge;

        return $this;
    }

    public function getSoldeCongeEx(): ?int
    {
        return $this->soldeCongeEx;
    }

    public function setSoldeCongeEx(?int $soldeCongeEx): self
    {
        $this->soldeCongeEx = $soldeCongeEx;

        return $this;
    }

    public function getEchelonId(): ?Echelon
    {
        return $this->echelonId;
    }
 
    public function setEchelonId(?Echelon $echelonId): self
    {
        $this->echelonId = $echelonId;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(?string $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getPosteBudget(): ?string
    {
        return $this->posteBudget;
    }

    public function setPosteBudget(?string $posteBudget): self
    {
        $this->posteBudget = $posteBudget;

        return $this;
    }

    public function getNbEnfant(): ?int
    {
        return $this->nbEnfant;
    }

    public function setNbEnfant(?int $nbEnfant): self
    {
        $this->nbEnfant = $nbEnfant;

        return $this;
    }

    public function getSituationFamiliale(): ?string
    {
        return $this->situationFamiliale;
    }

    public function setSituationFamiliale(?string $situationFamiliale): self
    {
        $this->situationFamiliale = $situationFamiliale;

        return $this;
    }

    public function getDateAffectationENSAT(): ?\DateTimeInterface
    {
        return $this->dateAffectationENSAT;
    }

    public function setDateAffectationENSAT(?\DateTimeInterface $dateAffectationENSAT): self
    {
        $this->dateAffectationENSAT = $dateAffectationENSAT;

        return $this;
    }

    public function getStructureRech(): ?StructRech
    {
        return $this->structureRech;
    }

    public function setStructureRech(?StructRech $structureRech): self
    {
        $this->structureRech = $structureRech;

        return $this;
    }

    public function getDateSoutenanceH(): ?\DateTimeInterface
    {
        return $this->dateSoutenanceH;
    }

    public function setDateSoutenanceH(?\DateTimeInterface $dateSoutenanceH): self
    {
        $this->dateSoutenanceH = $dateSoutenanceH;

        return $this;
    }

    public function getDatedeces(): ?\DateTimeInterface
    {
        return $this->datedeces;
    }

    public function setDatedeces(?\DateTimeInterface $datedeces): self
    {
        $this->datedeces = $datedeces;
        
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getServiceAffectationId(): ?Service
    {
        return $this->serviceAffectationId;
    }

    public function setServiceAffectationId(?Service $serviceAffectationId): self
    {
        $this->serviceAffectationId = $serviceAffectationId;

        return $this;
    }





    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setEmail(?int $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?int
    {
        return $this->email;
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

    /**
     * @return Collection<int, Avancement>
     */
    public function getAvancements(): Collection
    {
        return $this->avancements;
    }

    public function addAvancement(Avancement $avancement): self
    {
        if (!$this->avancements->contains($avancement)) {
            $this->avancements->add($avancement);
            $avancement->setPersonnel($this);
        }

        return $this;
    }

    public function removeAvancement(Avancement $avancement): self
    {
        if ($this->avancements->removeElement($avancement)) {
            // set the owning side to null (unless already changed)
            if ($avancement->getPersonnel() === $this) {
                $avancement->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, NoteFonctionnaire>
     */
    public function getNoteFonctionnaires(): Collection
    {
        return $this->noteFonctionnaires;
    }

    public function addNoteFonctionnaire(NoteFonctionnaire $noteFonctionnaire): self
    {
        if (!$this->noteFonctionnaires->contains($noteFonctionnaire)) {
            $this->noteFonctionnaires->add($noteFonctionnaire);
            $noteFonctionnaire->setPersonnel($this);
        }

        return $this;
    }

    public function removeNoteFonctionnaire(NoteFonctionnaire $noteFonctionnaire): self
    {
        if ($this->noteFonctionnaires->removeElement($noteFonctionnaire)) {
            // set the owning side to null (unless already changed)
            if ($noteFonctionnaire->getPersonnel() === $this) {
                $noteFonctionnaire->setPersonnel(null);
            }
        }

        return $this;
    }

    public function getActivite(): ?string
    {
        return $this->activite;
    }

    public function setActivite(?string $activite): self
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * @return Collection<int, SoldeConge>
     */
    public function getSoldeConges(): Collection
    {
        return $this->soldeConges;
    }

    public function addSoldeConge(SoldeConge $soldeConge): self
    {
        if (!$this->soldeConges->contains($soldeConge)) {
            $this->soldeConges->add($soldeConge);
            $soldeConge->setPersonnel($this);
        }

        return $this;
    }

    public function removeSoldeConge(SoldeConge $soldeConge): self
    {
        if ($this->soldeConges->removeElement($soldeConge)) {
            // set the owning side to null (unless already changed)
            if ($soldeConge->getPersonnel() === $this) {
                $soldeConge->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setPersonnel($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            // set the owning side to null (unless already changed)
            if ($document->getPersonnel() === $this) {
                $document->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Attestation>
     */
    public function getAttestations(): Collection
    {
        return $this->attestations;
    }

    public function addAttestation(Attestation $attestation): self
    {
        if (!$this->attestations->contains($attestation)) {
            $this->attestations->add($attestation);
            $attestation->setPersonnel($this);
        }

        return $this;
    }

    public function removeAttestation(Attestation $attestation): self
    {
        if ($this->attestations->removeElement($attestation)) {
            // set the owning side to null (unless already changed)
            if ($attestation->getPersonnel() === $this) {
                $attestation->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HistoDemandes>
     */
    public function getHistoDemandes(): Collection
    {
        return $this->histoDemandes;
    }

    public function addHistoDemande(HistoDemandes $histoDemande): self
    {
        if (!$this->histoDemandes->contains($histoDemande)) {
            $this->histoDemandes->add($histoDemande);
            $histoDemande->setDemandeur($this);
        }

        return $this;
    }

    public function removeHistoDemande(HistoDemandes $histoDemande): self
    {
        if ($this->histoDemandes->removeElement($histoDemande)) {
            // set the owning side to null (unless already changed)
            if ($histoDemande->getDemandeur() === $this) {
                $histoDemande->setDemandeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Autorisation>
     */
    public function getAutorisations(): Collection
    {
        return $this->autorisations;
    }

    public function addAutorisation(Autorisation $autorisation): self
    {
        if (!$this->autorisations->contains($autorisation)) {
            $this->autorisations->add($autorisation);
            $autorisation->setPersonnel($this);
        }

        return $this;
    }

    public function removeAutorisation(Autorisation $autorisation): self
    {
        if ($this->autorisations->removeElement($autorisation)) {
            // set the owning side to null (unless already changed)
            if ($autorisation->getPersonnel() === $this) {
                $autorisation->setPersonnel(null);
            }
        }

        return $this;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, HistoDemandes>
     */
    public function getHistoDemandesV(): Collection
    {
        return $this->histoDemandes_v;
    }

    public function addHistoDemandesV(HistoDemandes $histoDemandesV): self
    {
        if (!$this->histoDemandes_v->contains($histoDemandesV)) {
            $this->histoDemandes_v->add($histoDemandesV);
            $histoDemandesV->setValidateur($this);
        }

        return $this;
    }

    public function removeHistoDemandesV(HistoDemandes $histoDemandesV): self
    {
        if ($this->histoDemandes_v->removeElement($histoDemandesV)) {
            // set the owning side to null (unless already changed)
            if ($histoDemandesV->getValidateur() === $this) {
                $histoDemandesV->setValidateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conge>
     */
    public function getConges(): Collection
    {
        return $this->conges;
    }

    public function addConge(Conge $conge): self
    {
        if (!$this->conges->contains($conge)) {
            $this->conges->add($conge);
            $conge->setPersonnel($this);
        }

        return $this;
    }

    public function removeConge(Conge $conge): self
    {
        if ($this->conges->removeElement($conge)) {
            // set the owning side to null (unless already changed)
            if ($conge->getPersonnel() === $this) {
                $conge->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrdreMission>
     */
    public function getOrdreMissions(): Collection
    {
        return $this->ordreMissions;
    }

    public function addOrdreMission(OrdreMission $ordreMission): self
    {
        if (!$this->ordreMissions->contains($ordreMission)) {
            $this->ordreMissions->add($ordreMission);
            $ordreMission->setPersonnel($this);
        }

        return $this;
    }

    public function removeOrdreMission(OrdreMission $ordreMission): self
    {
        if ($this->ordreMissions->removeElement($ordreMission)) {
            // set the owning side to null (unless already changed)
            if ($ordreMission->getPersonnel() === $this) {
                $ordreMission->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Engagement>
     */
    public function getEngagements(): Collection
    {
        return $this->engagements;
    }

    public function addEngagement(Engagement $engagement): self
    {
        if (!$this->engagements->contains($engagement)) {
            $this->engagements->add($engagement);
            $engagement->setPersonnel($this);
        }

        return $this;
    }

    public function removeEngagement(Engagement $engagement): self
    {
        if ($this->engagements->removeElement($engagement)) {
            // set the owning side to null (unless already changed)
            if ($engagement->getPersonnel() === $this) {
                $engagement->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ficheheure>
     */
    public function getFicheheures(): Collection
    {
        return $this->ficheheures;
    }

    public function addFicheheure(Ficheheure $ficheheure): self
    {
        if (!$this->ficheheures->contains($ficheheure)) {
            $this->ficheheures->add($ficheheure);
            $ficheheure->setPersonnel($this);
        }

        return $this;
    }

    public function removeFicheheure(Ficheheure $ficheheure): self
    {
        if ($this->ficheheures->removeElement($ficheheure)) {
            // set the owning side to null (unless already changed)
            if ($ficheheure->getPersonnel() === $this) {
                $ficheheure->setPersonnel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EtuHistoDemandes>
     */
    public function getEtuHistoDemandes(): Collection
    {
        return $this->etuHistoDemandes;
    }

    public function addEtuHistoDemandes(EtuHistoDemandes $etuHistoDemandes): self
    {
        if (!$this->etuHistoDemandes->contains($etuHistoDemandes)) {
            $this->etuHistoDemandes->add($etuHistoDemandes);
            $etuHistoDemandes->setValidateur($this);
        }

        return $this;
    }

    public function removeEtuHistoDemandes(EtuHistoDemandes $etuHistoDemandes): self
    {
        if ($this->etuHistoDemandes->removeElement($etuHistoDemandes)) {
            // set the owning side to null (unless already changed)
            if ($etuHistoDemandes->setValidateur() === $this) {
                $etuHistoDemandes->setValidateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setResponsable($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getResponsable() === $this) {
                $paiement->setResponsable(null);
            }
        }

        return $this;
    }


    
    /**
     * @return Collection<int, FiliereFcResponsable>
     */
    public function getFiliereFcResponsables(): Collection
    {
        return $this->filiereFcResponsables;
    }

    public function addFiliereFcResponsable(FiliereFcResponsable $filiereFcResponsable): self
    {
        if (!$this->filiereFcResponsables->contains($filiereFcResponsable)) {
            $this->filiereFcResponsables->add($filiereFcResponsable);
            $filiereFcResponsable->setResponsable($this);
        }

        return $this;
    }

    public function removeFiliereFcResponsable(FiliereFcResponsable $filiereFcResponsable): self
    {
        if ($this->filiereFcResponsables->removeElement($filiereFcResponsable)) {
            // set the owning side to null (unless already changed)
            if ($filiereFcResponsable->getResponsable() === $this) {
                $filiereFcResponsable->setResponsable(null);
            }
        }

        return $this;
    }
  
  

 /**
     * @return Collection<int, Paiementprojet>
     */
    public function getPaiementprojets(): Collection
    {
        return $this->paiementprojets;
    }

    public function addPaiementprojet(Paiementprojet $paiementprojet): self
    {
        if (!$this->paiementprojets->contains($paiementprojet)) {
            $this->paiementprojets->add($paiementprojet);
            $paiementprojet->setResponsable($this);
        }

        return $this;
    }

    public function removePaiementprojet(Paiementprojet $paiementprojet): self
    {
        if ($this->paiementprojets->removeElement($paiementprojet)) {
            // set the owning side to null (unless already changed)
            if ($paiementprojet->getResponsable() === $this) {
                $paiementprojet->setResponsable(null);
            }
        }

        return $this;
    }

    public function getScopusId(): ?int
    {
        return $this->scopusId;
    }

    public function setScopusId(int $scopusId): self
    {
        $this->scopusId = $scopusId;

        return $this;
    }


/**
 * @return Collection<int, Publication>
 */
public function getPublications(): Collection
{
    return $this->publications;
}

public function addPublication(Publication $publication): self
{
    if (!$this->publications->contains($publication)) {
        $this->publications->add($publication);
        $publication->setPersonnel($this);
    }

    return $this;
}

public function removePublication(Publication $publication): self
{
    if ($this->publications->removeElement($publication)) {
        // Set the owning side to null (unless already changed)
        if ($publication->getPersonnel() === $this) {
            $publication->setPersonnel(null);
        }
    }

    return $this;
}

}
