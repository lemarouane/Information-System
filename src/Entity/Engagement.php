<?php

namespace App\Entity;

use App\Repository\EngagementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EngagementRepository::class)]
class Engagement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeEngagement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $matiere = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFait = null;

    #[ORM\ManyToOne(inversedBy: 'engagements' , cascade:["persist"])]
    private ?OrdreMission $ordreMission = null;

    #[ORM\ManyToOne(inversedBy: 'engagements')]
    private ?Personnel $personnel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeEngagement(): ?string
    {
        return $this->typeEngagement;
    }

    public function setTypeEngagement(?string $typeEngagement): self
    {
        $this->typeEngagement = $typeEngagement;

        return $this;
    }

    public function getMatiere(): ?string
    {
        return $this->matiere;
    }

    public function setMatiere(?string $matiere): self
    {
        $this->matiere = $matiere;

        return $this;
    }

    public function getDateFait(): ?\DateTimeInterface
    {
        return $this->dateFait;
    }

    public function setDateFait(?\DateTimeInterface $dateFait): self
    {
        $this->dateFait = $dateFait;

        return $this;
    }

    public function getOrdreMission(): ?OrdreMission
    {
        return $this->ordreMission;
    }

    public function setOrdreMission(?OrdreMission $ordreMission): self
    {
        $this->ordreMission = $ordreMission;

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
}
