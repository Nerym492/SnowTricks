<?php

namespace App\Entity;

use App\Repository\ImagesTrickRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesTrickRepository::class)]
class ImagesTrick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[ORM\ManyToOne(inversedBy: 'imagesTricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trick $trick = null;

    #[ORM\Column]
    private ?bool $isInTheHeader = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function isIsInTheHeader(): ?bool
    {
        return $this->isInTheHeader;
    }

    public function setIsInTheHeader(bool $isInTheHeader): static
    {
        $this->isInTheHeader = $isInTheHeader;

        return $this;
    }
}
