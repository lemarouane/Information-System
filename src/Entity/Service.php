<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomService = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roleSuperieur = null;

    #[ORM\Column(nullable: true)]
    private ?int $codes = null;

    #[ORM\OneToMany(mappedBy: 'serviceAffectationId', targetEntity: Personnel::class)]
    private Collection $personnels;

    public function __construct()
    {
        $this->personnels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomService(): ?string
    {
        return $this->nomService;
    }

    public function setNomService(?string $nomService): self
    {
        $this->nomService = $nomService;

        return $this;
    }


    public function getRoleSuperieur(): ?string
    {
        return $this->roleSuperieur;
    }

    public function setRoleSuperieur(?string $roleSuperieur): self
    {
        $this->roleSuperieur = $roleSuperieur;

        return $this;
    }


    public function getCodes(): ?int
    {
        return $this->codes;
    }

    public function setCodes(int $codes): self
    {
        $this->codes = $codes;

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
            $personnel->setServiceAffectationId($this);
        }

        return $this;
    }

    public function removePersonnel(Personnel $personnel): self
    {
        if ($this->personnels->removeElement($personnel)) {
            // set the owning side to null (unless already changed)
            if ($personnel->getServiceAffectationId() === $this) {
                $personnel->setServiceAffectationId(null);
            }
        }

        return $this;
    }
}
