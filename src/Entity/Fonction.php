<?php

namespace App\Entity;

use App\Repository\FonctionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FonctionRepository::class)]
class Fonction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fonctionExercee = null;

    #[ORM\OneToMany(mappedBy: 'fonctionExercee', targetEntity: Personnel::class)]
    private Collection $personnels;

    public function __construct()
    {
        $this->personnels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFonctionExercee(): ?string
    {
        return $this->fonctionExercee;
    }

    public function setFonctionExercee(?string $fonctionExercee): self
    {
        $this->fonctionExercee = $fonctionExercee;

        return $this;
    }

    /**
     * @return Collection<int, Personnel>
     */
    public function getPersonnels(): Collection
    {
        return $this->personnels;
    }

    public function addPersonnel(Personnel $personnel): self
    {
        if (!$this->personnels->contains($personnel)) {
            $this->personnels->add($personnel);
            $personnel->setFonctionExercee($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getFonctionExercee() === $this) {
                $personnel->setFonctionExercee(null);
            }
        }

        return $this;
    }
}
