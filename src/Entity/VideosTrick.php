<?php

namespace App\Entity;

use App\Repository\VideosTrickRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideosTrickRepository::class)]
class VideosTrick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'videosTricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Trick $trick_id = null;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTrickId(): ?Trick
    {
        return $this->trick_id;
    }

    public function setTrickId(?Trick $trick_id): self
    {
        $this->trick_id = $trick_id;

        return $this;
    }
}
