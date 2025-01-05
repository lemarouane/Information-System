<?php

namespace App\Entity;

use App\Repository\DirectionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DirectionRepository::class)]
class Direction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle_dir = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleDir(): ?string
    {
        return $this->libelle_dir;
    }

    public function setLibelleDir(?string $libelle_dir): self
    {
        $this->libelle_dir = $libelle_dir;

        return $this;
    }
}
