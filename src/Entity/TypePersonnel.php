<?php

namespace App\Entity;

use App\Repository\TypePersonnelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypePersonnelRepository::class)]
class TypePersonnel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libellePersonnel = null;

    #[ORM\OneToMany(mappedBy: 'typePersonnelId', targetEntity: Personnel::class)]
    private Collection $personnels;

    public function __construct()
    {
        $this->personnels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibellePersonnel(): ?string
    {
        return $this->libellePersonnel;
    }

    public function setLibellePersonnel(?string $libellePersonnel): self
    {
        $this->libellePersonnel = $libellePersonnel;

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
            $personnel->setTypePersonnelId($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getTypePersonnelId() === $this) {
                $personnel->setTypePersonnelId(null);
            }
        }

        return $this;
    }
}
