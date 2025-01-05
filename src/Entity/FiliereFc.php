<?php

namespace App\Entity;

use App\Repository\FiliereFcRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiliereFcRepository::class)]
class FiliereFc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomFiliere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $codeApo = null;

    #[ORM\OneToMany(mappedBy: 'filiere_fc', targetEntity: FiliereFcResponsable::class)]
    private Collection $filiereFcResponsables;

    #[ORM\Column(nullable: true)]
    private ?int $code_version = null;

    public function __construct()
    {
        $this->filiereFcResponsables = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFiliere(): ?string
    {
        return $this->nomFiliere;
    }

    public function setNomFiliere(?string $nomFiliere): self
    {
        $this->nomFiliere = $nomFiliere;

        return $this;
    }

    public function getCodeApo(): ?string
    {
        return $this->codeApo;
    }

    public function setCodeApo(?string $codeApo): self
    {
        $this->codeApo = $codeApo;

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
            $filiereFcResponsable->setFiliereFc($this);
        }

        return $this;
    }

    public function removeFiliereFcResponsable(FiliereFcResponsable $filiereFcResponsable): self
    {
        if ($this->filiereFcResponsables->removeElement($filiereFcResponsable)) {
            // set the owning side to null (unless already changed)
            if ($filiereFcResponsable->getFiliereFc() === $this) {
                $filiereFcResponsable->setFiliereFc(null);
            }
        }

        return $this;
    }

    public function getCodeVersion(): ?int
    {
        return $this->code_version;
    }

    public function setCodeVersion(?int $code_version): self
    {
        $this->code_version = $code_version;

        return $this;
    }
}
