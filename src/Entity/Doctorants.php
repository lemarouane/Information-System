<?php

namespace App\Entity;

use App\Repository\DoctorantsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctorantsRepository::class)]
#[ORM\Table(name: "doctorants", schema: "pgi_ensa_db")]

class Doctorants
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    

    #[ORM\Column(length: 255)]
    private ?string $Etablissement = null;

    #[ORM\Column(length: 255)]
    private ?string $Nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nomArabe = null;

    #[ORM\Column(length: 255)]
    private ?string $prenomArabe = null;

    #[ORM\Column(length: 255)]
    private ?string $cin = null;

    #[ORM\Column(length: 255)]
    private ?string $cne = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDeNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $lieuDeNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $provinceDeNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $paysNaissance = null;

    #[ORM\Column(length: 255)]
    private ?string $nationalite = null;

    #[ORM\Column(length: 255)]
    private ?string $sexe = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $emailInstitutionnel = null;

    #[ORM\Column(length: 255)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255)]
    private ?string $organismeEmployeur = null;

    #[ORM\Column(length: 255)]
    private ?string $profession = null;

    #[ORM\Column(length: 255)]
    private ?string $academieBac = null;

    #[ORM\Column(length: 255)]
    private ?string $provinceBac = null;

    #[ORM\Column(length: 255)]
    private ?string $lyceeBac = null;

    #[ORM\Column(length: 255)]
    private ?string $mentionBac = null;

    #[ORM\Column(length: 255)]
    private ?string $noteBac = null;

    #[ORM\Column(length: 255)]
    private ?string $dateObtentionBac = null;

    #[ORM\Column(length: 255)]
    private ?string $paysBac = null;

    #[ORM\Column(length: 255)]
    private ?string $secteurBac = null;

    #[ORM\Column(length: 255)]
    private ?string $specialiteBac = null;

    #[ORM\Column(length: 255)]
    private ?string $licenceEtablissement = null;

    #[ORM\Column(length: 255)]
    private ?string $mentionLicence = null;

    #[ORM\Column(length: 255)]
    private ?string $dateObtentionLicence = null;

    #[ORM\Column(length: 255)]
    private ?string $licencePays = null;

    #[ORM\Column(length: 255)]
    private ?string $licenceSpecialite = null;

    #[ORM\Column(length: 255)]
    private ?string $universiteLicence = null;

    #[ORM\Column(length: 255)]
    private ?string $noteLicence = null;

    #[ORM\Column(length: 255)]
    private ?string $licenceSecteur = null;

    #[ORM\Column(length: 255)]
    private ?string $masterEtablissement = null;

    #[ORM\Column(length: 255)]
    private ?string $noteMaster = null;

    #[ORM\Column(length: 255)]
    private ?string $mentionMaster = null;

    #[ORM\Column(length: 255)]
    private ?string $dateObtentionMaster = null;

    #[ORM\Column(length: 255)]
    private ?string $paysObtentionMaster = null;

    #[ORM\Column(length: 255)]
    private ?string $masterSpecialite = null;

    #[ORM\Column(length: 255)]
    private ?string $masterUniversite = null;

    #[ORM\Column(length: 255)]
    private ?string $masterSecteur = null;

    #[ORM\Column(length: 255)]
    private ?string $autreDiplomeType = null;

    #[ORM\Column(length: 255)]
    private ?string $autreDiplomeEtablissement = null;

    #[ORM\Column(length: 255)]
    private ?string $autreDiplomeMention = null;

    #[ORM\Column(length: 255)]
    private ?string $autreDiplomeDateObtention = null;

    #[ORM\Column(length: 255)]
    private ?string $autreDiplomePays = null;

    #[ORM\Column(length: 255)]
    private ?string $autreDiplomeSpecialite = null;

    #[ORM\Column(length: 255)]
    private ?string $autreDiplomeUniversite = null;

    #[ORM\Column(length: 255)]
    private ?string $autreDiplomeSecteur = null;

    #[ORM\Column(length: 255)]
    private ?string $langue1 = null;

    #[ORM\Column(length: 255)]
    private ?string $langue2 = null;

    #[ORM\Column(length: 255)]
    private ?string $langue3 = null;

    #[ORM\Column(length: 255)]
    private ?string $langue4 = null;

    #[ORM\Column(length: 255)]
    private ?string $niveauLangue1 = null;

    #[ORM\Column(length: 255)]
    private ?string $niveauLangue2 = null;

    #[ORM\Column(length: 255)]
    private ?string $niveauLangue3 = null;

    #[ORM\Column(length: 255)]
    private ?string $niveauLangue4 = null;

    #[ORM\Column(length: 255)]
    private ?string $etablissement1 = null;

    #[ORM\Column(length: 255)]
    private ?string $etablissement2 = null;

    #[ORM\Column(length: 255)]
    private ?string $etablissement3 = null;

    #[ORM\Column(length: 255)]
    private ?string $experience1 = null;

    #[ORM\Column(length: 255)]
    private ?string $experience2 = null;

    #[ORM\Column(length: 255)]
    private ?string $experience3 = null;

    #[ORM\Column(length: 255)]
    private ?string $fonction1 = null;

    #[ORM\Column(length: 255)]
    private ?string $fonction2 = null;

    #[ORM\Column(length: 255)]
    private ?string $fonction3 = null;

    #[ORM\Column(length: 255)]
    private ?string $periode1D = null;

    #[ORM\Column(length: 255)]
    private ?string $periode1F = null;

    #[ORM\Column(length: 255)]
    private ?string $periode2D = null;

    #[ORM\Column(length: 255)]
    private ?string $periode2F = null;

    #[ORM\Column(length: 255)]
    private ?string $periode3D = null;

    #[ORM\Column(length: 255)]
    private ?string $periode3F = null;

    #[ORM\Column(length: 255)]
    private ?string $secteur1 = null;

    #[ORM\Column(length: 255)]
    private ?string $secteur2 = null;

    #[ORM\Column(length: 255)]
    private ?string $secteur3 = null;

    #[ORM\Column(length: 255)]
    private ?string $handicape = null;

    #[ORM\Column(length: 255)]
    private ?string $ced = null;

    #[ORM\Column(length: 255)]
    private ?string $fd = null;

    #[ORM\Column(length: 255)]
    private ?string $enseignantChercheur = null;

    #[ORM\Column(length: 255)]
    private ?string $choix = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\Column(length: 255)]
    private ?string $n_etabliss = null;

    #[ORM\Column(length: 255)]
    private ?string $dateEnvoi = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtablissement(): ?string
    {
        return $this->Etablissement;
    }

    public function setEtablissement(string $Etablissement): self
    {
        $this->Etablissement = $Etablissement;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

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

    public function getNomArabe(): ?string
    {
        return $this->nomArabe;
    }

    public function setNomArabe(string $nomArabe): self
    {
        $this->nomArabe = $nomArabe;

        return $this;
    }

    public function getPrenomArabe(): ?string
    {
        return $this->prenomArabe;
    }

    public function setPrenomArabe(string $prenomArabe): self
    {
        $this->prenomArabe = $prenomArabe;

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

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->dateDeNaissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $dateDeNaissance): self
    {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    public function getLieuDeNaissance(): ?string
    {
        return $this->lieuDeNaissance;
    }

    public function setLieuDeNaissance(string $lieuDeNaissance): self
    {
        $this->lieuDeNaissance = $lieuDeNaissance;

        return $this;
    }

    public function getProvinceDeNaissance(): ?string
    {
        return $this->provinceDeNaissance;
    }

    public function setProvinceDeNaissance(string $provinceDeNaissance): self
    {
        $this->provinceDeNaissance = $provinceDeNaissance;

        return $this;
    }

    public function getPaysNaissance(): ?string
    {
        return $this->paysNaissance;
    }

    public function setPaysNaissance(string $paysNaissance): self
    {
        $this->paysNaissance = $paysNaissance;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(string $nationalite): self
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
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

    public function getEmailInstitutionnel(): ?string
    {
        return $this->emailInstitutionnel;
    }

    public function setEmailInstitutionnel(string $emailInstitutionnel): self
    {
        $this->emailInstitutionnel = $emailInstitutionnel;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getOrganismeEmployeur(): ?string
    {
        return $this->organismeEmployeur;
    }

    public function setOrganismeEmployeur(string $organismeEmployeur): self
    {
        $this->organismeEmployeur = $organismeEmployeur;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getAcademieBac(): ?string
    {
        return $this->academieBac;
    }

    public function setAcademieBac(string $academieBac): self
    {
        $this->academieBac = $academieBac;

        return $this;
    }

    public function getProvinceBac(): ?string
    {
        return $this->provinceBac;
    }

    public function setProvinceBac(string $provinceBac): self
    {
        $this->provinceBac = $provinceBac;

        return $this;
    }

    public function getLyceeBac(): ?string
    {
        return $this->lyceeBac;
    }

    public function setLyceeBac(string $lyceeBac): self
    {
        $this->lyceeBac = $lyceeBac;

        return $this;
    }

    public function getMentionBac(): ?string
    {
        return $this->mentionBac;
    }

    public function setMentionBac(string $mentionBac): self
    {
        $this->mentionBac = $mentionBac;

        return $this;
    }

    public function getNoteBac(): ?string
    {
        return $this->noteBac;
    }

    public function setNoteBac(string $noteBac): self
    {
        $this->noteBac = $noteBac;

        return $this;
    }

    public function getDateObtentionBac(): ?string
    {
        return $this->dateObtentionBac;
    }

    public function setDateObtentionBac(string $dateObtentionBac): self
    {
        $this->dateObtentionBac = $dateObtentionBac;

        return $this;
    }

    public function getPaysBac(): ?string
    {
        return $this->paysBac;
    }

    public function setPaysBac(string $paysBac): self
    {
        $this->paysBac = $paysBac;

        return $this;
    }

    public function getSecteurBac(): ?string
    {
        return $this->secteurBac;
    }

    public function setSecteurBac(string $secteurBac): self
    {
        $this->secteurBac = $secteurBac;

        return $this;
    }

    public function getSpecialiteBac(): ?string
    {
        return $this->specialiteBac;
    }

    public function setSpecialiteBac(string $specialiteBac): self
    {
        $this->specialiteBac = $specialiteBac;

        return $this;
    }

    public function getLicenceEtablissement(): ?string
    {
        return $this->licenceEtablissement;
    }

    public function setLicenceEtablissement(string $licenceEtablissement): self
    {
        $this->licenceEtablissement = $licenceEtablissement;

        return $this;
    }

    public function getMentionLicence(): ?string
    {
        return $this->mentionLicence;
    }

    public function setMentionLicence(string $mentionLicence): self
    {
        $this->mentionLicence = $mentionLicence;

        return $this;
    }

    public function getDateObtentionLicence(): ?string
    {
        return $this->dateObtentionLicence;
    }

    public function setDateObtentionLicence(string $dateObtentionLicence): self
    {
        $this->dateObtentionLicence = $dateObtentionLicence;

        return $this;
    }

    public function getLicencePays(): ?string
    {
        return $this->licencePays;
    }

    public function setLicencePays(string $licencePays): self
    {
        $this->licencePays = $licencePays;

        return $this;
    }

    public function getLicenceSpecialite(): ?string
    {
        return $this->licenceSpecialite;
    }

    public function setLicenceSpecialite(string $licenceSpecialite): self
    {
        $this->licenceSpecialite = $licenceSpecialite;

        return $this;
    }

    public function getUniversiteLicence(): ?string
    {
        return $this->universiteLicence;
    }

    public function setUniversiteLicence(string $universiteLicence): self
    {
        $this->universiteLicence = $universiteLicence;

        return $this;
    }

    public function getNoteLicence(): ?string
    {
        return $this->noteLicence;
    }

    public function setNoteLicence(string $noteLicence): self
    {
        $this->noteLicence = $noteLicence;

        return $this;
    }

    public function getLicenceSecteur(): ?string
    {
        return $this->licenceSecteur;
    }

    public function setLicenceSecteur(string $licenceSecteur): self
    {
        $this->licenceSecteur = $licenceSecteur;

        return $this;
    }

    public function getMasterEtablissement(): ?string
    {
        return $this->masterEtablissement;
    }

    public function setMasterEtablissement(string $masterEtablissement): self
    {
        $this->masterEtablissement = $masterEtablissement;

        return $this;
    }

    public function getNoteMaster(): ?string
    {
        return $this->noteMaster;
    }

    public function setNoteMaster(string $noteMaster): self
    {
        $this->noteMaster = $noteMaster;

        return $this;
    }

    public function getMentionMaster(): ?string
    {
        return $this->mentionMaster;
    }

    public function setMentionMaster(string $mentionMaster): self
    {
        $this->mentionMaster = $mentionMaster;

        return $this;
    }

    public function getDateObtentionMaster(): ?string
    {
        return $this->dateObtentionMaster;
    }

    public function setDateObtentionMaster(string $dateObtentionMaster): self
    {
        $this->dateObtentionMaster = $dateObtentionMaster;

        return $this;
    }

    public function getPaysObtentionMaster(): ?string
    {
        return $this->paysObtentionMaster;
    }

    public function setPaysObtentionMaster(string $paysObtentionMaster): self
    {
        $this->paysObtentionMaster = $paysObtentionMaster;

        return $this;
    }

    public function getMasterSpecialite(): ?string
    {
        return $this->masterSpecialite;
    }

    public function setMasterSpecialite(string $masterSpecialite): self
    {
        $this->masterSpecialite = $masterSpecialite;

        return $this;
    }

    public function getMasterUniversite(): ?string
    {
        return $this->masterUniversite;
    }

    public function setMasterUniversite(string $masterUniversite): self
    {
        $this->masterUniversite = $masterUniversite;

        return $this;
    }

    public function getMasterSecteur(): ?string
    {
        return $this->masterSecteur;
    }

    public function setMasterSecteur(string $masterSecteur): self
    {
        $this->masterSecteur = $masterSecteur;

        return $this;
    }

    public function getAutreDiplomeType(): ?string
    {
        return $this->autreDiplomeType;
    }

    public function setAutreDiplomeType(string $autreDiplomeType): self
    {
        $this->autreDiplomeType = $autreDiplomeType;

        return $this;
    }

    public function getAutreDiplomeEtablissement(): ?string
    {
        return $this->autreDiplomeEtablissement;
    }

    public function setAutreDiplomeEtablissement(string $autreDiplomeEtablissement): self
    {
        $this->autreDiplomeEtablissement = $autreDiplomeEtablissement;

        return $this;
    }

    public function getAutreDiplomeMention(): ?string
    {
        return $this->autreDiplomeMention;
    }

    public function setAutreDiplomeMention(string $autreDiplomeMention): self
    {
        $this->autreDiplomeMention = $autreDiplomeMention;

        return $this;
    }

    public function getAutreDiplomeDateObtention(): ?string
    {
        return $this->autreDiplomeDateObtention;
    }

    public function setAutreDiplomeDateObtention(string $autreDiplomeDateObtention): self
    {
        $this->autreDiplomeDateObtention = $autreDiplomeDateObtention;

        return $this;
    }

    public function getAutreDiplomePays(): ?string
    {
        return $this->autreDiplomePays;
    }

    public function setAutreDiplomePays(string $autreDiplomePays): self
    {
        $this->autreDiplomePays = $autreDiplomePays;

        return $this;
    }

    public function getAutreDiplomeSpecialite(): ?string
    {
        return $this->autreDiplomeSpecialite;
    }

    public function setAutreDiplomeSpecialite(string $autreDiplomeSpecialite): self
    {
        $this->autreDiplomeSpecialite = $autreDiplomeSpecialite;

        return $this;
    }

    public function getAutreDiplomeUniversite(): ?string
    {
        return $this->autreDiplomeUniversite;
    }

    public function setAutreDiplomeUniversite(string $autreDiplomeUniversite): self
    {
        $this->autreDiplomeUniversite = $autreDiplomeUniversite;

        return $this;
    }

    public function getAutreDiplomeSecteur(): ?string
    {
        return $this->autreDiplomeSecteur;
    }

    public function setAutreDiplomeSecteur(string $autreDiplomeSecteur): self
    {
        $this->autreDiplomeSecteur = $autreDiplomeSecteur;

        return $this;
    }

    public function getLangue1(): ?string
    {
        return $this->langue1;
    }

    public function setLangue1(string $langue1): self
    {
        $this->langue1 = $langue1;

        return $this;
    }

    public function getLangue2(): ?string
    {
        return $this->langue2;
    }

    public function setLangue2(string $langue2): self
    {
        $this->langue2 = $langue2;

        return $this;
    }

    public function getLangue3(): ?string
    {
        return $this->langue3;
    }

    public function setLangue3(string $langue3): self
    {
        $this->langue3 = $langue3;

        return $this;
    }

    public function getLangue4(): ?string
    {
        return $this->langue4;
    }

    public function setLangue4(string $langue4): self
    {
        $this->langue4 = $langue4;

        return $this;
    }

    public function getNiveauLangue1(): ?string
    {
        return $this->niveauLangue1;
    }

    public function setNiveauLangue1(string $niveauLangue1): self
    {
        $this->niveauLangue1 = $niveauLangue1;

        return $this;
    }

    public function getNiveauLangue2(): ?string
    {
        return $this->niveauLangue2;
    }

    public function setNiveauLangue2(string $niveauLangue2): self
    {
        $this->niveauLangue2 = $niveauLangue2;

        return $this;
    }

    public function getNiveauLangue3(): ?string
    {
        return $this->niveauLangue3;
    }

    public function setNiveauLangue3(string $niveauLangue3): self
    {
        $this->niveauLangue3 = $niveauLangue3;

        return $this;
    }

    public function getNiveauLangue4(): ?string
    {
        return $this->niveauLangue4;
    }

    public function setNiveauLangue4(string $niveauLangue4): self
    {
        $this->niveauLangue4 = $niveauLangue4;

        return $this;
    }

    public function getEtablissement1(): ?string
    {
        return $this->etablissement1;
    }

    public function setEtablissement1(string $etablissement1): self
    {
        $this->etablissement1 = $etablissement1;

        return $this;
    }

    public function getEtablissement2(): ?string
    {
        return $this->etablissement2;
    }

    public function setEtablissement2(string $etablissement2): self
    {
        $this->etablissement2 = $etablissement2;

        return $this;
    }

    public function getEtablissement3(): ?string
    {
        return $this->etablissement3;
    }

    public function setEtablissement3(string $etablissement3): self
    {
        $this->etablissement3 = $etablissement3;

        return $this;
    }

    public function getExperience1(): ?string
    {
        return $this->experience1;
    }

    public function setExperience1(string $experience1): self
    {
        $this->experience1 = $experience1;

        return $this;
    }

    public function getExperience2(): ?string
    {
        return $this->experience2;
    }

    public function setExperience2(string $experience2): self
    {
        $this->experience2 = $experience2;

        return $this;
    }

    public function getExperience3(): ?string
    {
        return $this->experience3;
    }

    public function setExperience3(string $experience3): self
    {
        $this->experience3 = $experience3;

        return $this;
    }

    public function getFonction1(): ?string
    {
        return $this->fonction1;
    }

    public function setFonction1(string $fonction1): self
    {
        $this->fonction1 = $fonction1;

        return $this;
    }

    public function getFonction2(): ?string
    {
        return $this->fonction2;
    }

    public function setFonction2(string $fonction2): self
    {
        $this->fonction2 = $fonction2;

        return $this;
    }

    public function getFonction3(): ?string
    {
        return $this->fonction3;
    }

    public function setFonction3(string $fonction3): self
    {
        $this->fonction3 = $fonction3;

        return $this;
    }

    public function getPeriode1D(): ?string
    {
        return $this->periode1D;
    }

    public function setPeriode1D(string $periode1D): self
    {
        $this->periode1D = $periode1D;

        return $this;
    }

    public function getPeriode1F(): ?string
    {
        return $this->periode1F;
    }

    public function setPeriode1F(string $periode1F): self
    {
        $this->periode1F = $periode1F;

        return $this;
    }

    public function getPeriode2D(): ?string
    {
        return $this->periode2D;
    }

    public function setPeriode2D(string $periode2D): self
    {
        $this->periode2D = $periode2D;

        return $this;
    }

    public function getPeriode2F(): ?string
    {
        return $this->periode2F;
    }

    public function setPeriode2F(string $periode2F): self
    {
        $this->periode2F = $periode2F;

        return $this;
    }

    public function getPeriode3D(): ?string
    {
        return $this->periode3D;
    }

    public function setPeriode3D(string $periode3D): self
    {
        $this->periode3D = $periode3D;

        return $this;
    }

    public function getPeriode3F(): ?string
    {
        return $this->periode3F;
    }

    public function setPeriode3F(string $periode3F): self
    {
        $this->periode3F = $periode3F;

        return $this;
    }

    public function getSecteur1(): ?string
    {
        return $this->secteur1;
    }

    public function setSecteur1(string $secteur1): self
    {
        $this->secteur1 = $secteur1;

        return $this;
    }

    public function getSecteur2(): ?string
    {
        return $this->secteur2;
    }

    public function setSecteur2(string $secteur2): self
    {
        $this->secteur2 = $secteur2;

        return $this;
    }

    public function getSecteur3(): ?string
    {
        return $this->secteur3;
    }

    public function setSecteur3(string $secteur3): self
    {
        $this->secteur3 = $secteur3;

        return $this;
    }

    public function getHandicape(): ?string
    {
        return $this->handicape;
    }

    public function setHandicape(string $handicape): self
    {
        $this->handicape = $handicape;

        return $this;
    }

    public function getCed(): ?string
    {
        return $this->ced;
    }

    public function setCed(string $ced): self
    {
        $this->ced = $ced;

        return $this;
    }

    public function getFd(): ?string
    {
        return $this->fd;
    }

    public function setFd(string $fd): self
    {
        $this->fd = $fd;

        return $this;
    }

    public function getEnseignantChercheur(): ?string
    {
        return $this->enseignantChercheur;
    }

    public function setEnseignantChercheur(string $enseignantChercheur): self
    {
        $this->enseignantChercheur = $enseignantChercheur;

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

    public function getNEtabliss(): ?string
    {
        return $this->n_etabliss;
    }

    public function setNEtabliss(string $n_etabliss): self
    {
        $this->n_etabliss = $n_etabliss;

        return $this;
    }

    public function getDateEnvoi(): ?string
    {
        return $this->dateEnvoi;
    }

    public function setDateEnvoi(string $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }
}
