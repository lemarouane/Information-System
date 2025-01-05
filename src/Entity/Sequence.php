<?php

namespace App\Entity;

use App\Repository\SequenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SequenceRepository::class)]
class Sequence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $entity = null;

    #[ORM\Column]
    private ?int $nextval = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getNextval(): ?int
    {
        return $this->nextval;
    }

    public function setNextval(int $nextval): self
    {
        $this->nextval = $nextval;

        return $this;
    }
}
