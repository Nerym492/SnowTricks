<?php

namespace App\Entity;

use App\Repository\ImagesTrickRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * ImagesTrick entity
 */
#[ORM\Entity(repositoryClass: ImagesTrickRepository::class)]
class ImagesTrick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[ORM\ManyToOne(inversedBy: 'imagesTricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trick $trick = null;

    #[ORM\Column]
    private ?bool $isInTheHeader = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return Trick|null
     */
    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    /**
     * @param Trick|null $trick
     * @return $this
     */
    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isIsInTheHeader(): ?bool
    {
        return $this->isInTheHeader;
    }

    /**
     * @param bool $isInTheHeader
     * @return $this
     */
    public function setIsInTheHeader(bool $isInTheHeader): static
    {
        $this->isInTheHeader = $isInTheHeader;

        return $this;
    }
}
