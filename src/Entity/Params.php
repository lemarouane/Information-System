<?php

namespace App\Entity;

use App\Repository\ParamsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
#[ORM\Entity(repositoryClass: ParamsRepository::class)]
class Params
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slogan = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomApp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $version = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[Vich\UploadableField(mapping: 'params', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string',nullable : true)]
    private ?string $imageName = null;

    #[ORM\Column(type: 'integer',nullable : true)]
    private ?int $imageSize = null;

    #[Vich\UploadableField(mapping: 'params', fileNameProperty: 'imageName2', size: 'imageSize2')]
    private ?File $imageFile2 = null;

    #[ORM\Column(type: 'string',nullable : true)]
    private ?string $imageName2 = null;

    #[ORM\Column(type: 'integer',nullable : true)]
    private ?int $imageSize2 = null;
    
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getSlogan(): ?string
    {
        return $this->slogan;
    }

    public function setSlogan(?string $slogan): self
    {
        $this->slogan = $slogan;

        return $this;
    }

    public function getNomApp(): ?string
    {
        return $this->nomApp;
    }

    public function setNomApp(?string $nomApp): self
    {
        $this->nomApp = $nomApp;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }













    public function getImageFile2(): ?File
    {
        return $this->imageFile2;
    }

    public function setImageName2(?string $imageName2): void
    {
        $this->imageName2 = $imageName2;
    }

    public function getImageName2(): ?string
    {
        return $this->imageName2;
    }

    public function setImageSize2(?int $imageSize2): void
    {
        $this->imageSize2 = $imageSize2;
    }

    public function getImageSize2(): ?int
    {
        return $this->imageSize2;
    }







    
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    
}
