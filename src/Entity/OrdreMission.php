<?php

namespace App\Entity;

use App\Repository\OrdreMissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrdreMissionRepository::class)]
class OrdreMission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeMission = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cadreMission = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $objetMission = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $structureAcceuil = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateEnvoie = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $valeurAutre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $valeurProjet = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $invitation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $modif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $niveau = null;

    #[ORM\ManyToOne(inversedBy: 'ordreMissions')]
    private ?Personnel $personnel = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $financementMission = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $financementvoyage = [];

    #[ORM\OneToMany(mappedBy: 'ordreMission', targetEntity: Engagement::class , cascade:["persist","remove"])]
    private Collection $engagements;

    #[ORM\Column(nullable: true)]
    private ?bool $bloque = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $moyenTransport = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $marqueauto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $matriculeauto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typedest = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $valeurfc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $valeurprojetvg = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $valeurautrevg = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $valeurfcvg = null;

    public function __construct()
    {
        $this->engagements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeMission(): ?string
    {
        return $this->typeMission;
    }

    public function setTypeMission(?string $typeMission): self
    {
        $this->typeMission = $typeMission;

        return $this;
    }

    public function getCadreMission(): ?string
    {
        return $this->cadreMission;
    }

    public function setCadreMission(?string $cadreMission): self
    {
        $this->cadreMission = $cadreMission;

        return $this;
    }

    public function getObjetMission(): ?string
    {
        return $this->objetMission;
    }

    public function setObjetMission(?string $objetMission): self
    {
        $this->objetMission = $objetMission;

        return $this;
    }

    public function getStructureAcceuil(): ?string
    {
        return $this->structureAcceuil;
    }

    public function setStructureAcceuil(?string $structureAcceuil): self
    {
        $this->structureAcceuil = $structureAcceuil;

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

    public function getValeurAutre(): ?string
    {
        return $this->valeurAutre;
    }

    public function setValeurAutre(?string $valeurAutre): self
    {
        $this->valeurAutre = $valeurAutre;

        return $this;
    }

    public function getValeurProjet(): ?string
    {
        return $this->valeurProjet;
    }

    public function setValeurProjet(?string $valeurProjet): self
    {
        $this->valeurProjet = $valeurProjet;

        return $this;
    }

    public function getInvitation(): ?string
    {
        return $this->invitation;
    }

    public function setInvitation(?string $invitation): self
    {
        $this->invitation = $invitation;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getModif(): ?string
    {
        return $this->modif;
    }

    public function setModif(?string $modif): self
    {
        $this->modif = $modif;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): self
    {
        $this->lien = $lien;

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

    public function getPersonnel(): ?Personnel
    {
        return $this->personnel;
    }

    public function setPersonnel(?Personnel $personnel): self
    {
        $this->personnel = $personnel;

        return $this;
    }

    public function getFinancementMission(): array
    {
        return $this->financementMission;
    }

    public function setFinancementMission(?array $financementMission): self
    {
        $this->financementMission = $financementMission;

        return $this;
    }
    
    public function getFinancementvoyage(): array
    {
        return $this->financementvoyage;
    }

    public function setFinancementvoyage(?array $financementvoyage): self
    {
        $this->financementvoyage = $financementvoyage;

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
            $engagement->setOrdreMission($this);
        }

        return $this;
    }

    public function removeEngagement(Engagement $engagement): self
    {
        if ($this->engagements->removeElement($engagement)) {
            // set the owning side to null (unless already changed)
            if ($engagement->getOrdreMission() === $this) {
                $engagement->setOrdreMission(null);
            }
        }

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

    public function getMoyenTransport(): ?string
    {
        return $this->moyenTransport;
    }

    public function setMoyenTransport(?string $moyenTransport): self
    {
        $this->moyenTransport = $moyenTransport;

        return $this;
    }

    
    public function getMarqueauto(): ?string
    {
        return $this->marqueauto;
    }

    public function setMarqueauto(?string $marqueauto): self
    {
        $this->marqueauto = $marqueauto;

        return $this;
    }

    public function getMatriculeauto(): ?string
    {
        return $this->matriculeauto;
    }

    public function setMatriculeauto(?string $matriculeauto): self
    {
        $this->matriculeauto = $matriculeauto;

        return $this;
    }

    public function getTypedest(): ?string
    {
        return $this->typedest;
    }

    public function setTypedest(?string $typedest): self
    {
        $this->typedest = $typedest;

        return $this;
    }

    public function getValeurfc(): ?string
    {
        return $this->valeurfc;
    }

    public function setValeurfc(?string $valeurfc): self
    {
        $this->valeurfc = $valeurfc;

        return $this;
    }

    public function getValeurprojetvg(): ?string
    {
        return $this->valeurprojetvg;
    }

    public function setValeurprojetvg(?string $valeurprojetvg): self
    {
        $this->valeurprojetvg = $valeurprojetvg;

        return $this;
    }

    public function getValeurautrevg(): ?string
    {
        return $this->valeurautrevg;
    }

    public function setValeurautrevg(?string $valeurautrevg): self
    {
        $this->valeurautrevg = $valeurautrevg;

        return $this;
    }

    public function getValeurfcvg(): ?string
    {
        return $this->valeurfcvg;
    }

    public function setValeurfcvg(?string $valeurfcvg): self
    {
        $this->valeurfcvg = $valeurfcvg;

        return $this;
    }
}
