<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigRepository::class)]
class Config
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $theKey = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $theValue = null;

    public function __construct($the_key='', $the_value='') {
        $this->the_key = $the_key;
        $this->the_value = $the_value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTheKey(): ?string
    {
        return $this->theKey;
    }

    public function setTheKey(?string $theKey): self
    {
        $this->theKey = $theKey;

        return $this;
    }

    public function getTheValue(): ?string
    {
        return $this->theValue;
    }

    public function setTheValue(?string $theValue): self
    {
        $this->theValue = $theValue;

        return $this;
    }
}
