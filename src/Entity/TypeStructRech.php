<?php

namespace App\Entity;

use App\Repository\TypeStructRechRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeStructRechRepository::class)]
class TypeStructRech
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomStructure = null;

    #[ORM\OneToMany(mappedBy: 'typeStructure', targetEntity: StructRech::class)]
    private Collection $structReches;

    public function __construct()
    {
        $this->structReches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomStructure(): ?string
    {
        return $this->nomStructure;
    }

    public function setNomStructure(?string $nomStructure): self
    {
        $this->nomStructure = $nomStructure;

        return $this;
    }

    /**
     * @return Collection<int, StructRech>
     */
    public function getStructReches(): Collection
    {
        return $this->structReches;
    }

    public function addStructRech(StructRech $structRech): self
    {
        if (!$this->structReches->contains($structRech)) {
            $this->structReches->add($structRech);
            $structRech->setTypeStructure($this);
        }

        return $this;
    }

    public function removeStructRech(StructRech $structRech): self
    {
        if ($this->structReches->removeElement($structRech)) {
            // set the owning side to null (unless already changed)
            if ($structRech->getTypeStructure() === $this) {
                $structRech->setTypeStructure(null);
            }
        }

        return $this;
    }
}
