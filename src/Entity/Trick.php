<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupeTrick $groupe_trick_id = null;

    #[ORM\OneToMany(mappedBy: 'trick_id', targetEntity: ImagesTrick::class, orphanRemoval: true)]
    private Collection $imagesTricks;

    #[ORM\OneToMany(mappedBy: 'trick_id', targetEntity: VideosTrick::class, orphanRemoval: true)]
    private Collection $videosTricks;

    public function __construct()
    {
        $this->imagesTricks = new ArrayCollection();
        $this->videosTricks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
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

    public function getGroupeTrickId(): ?GroupeTrick
    {
        return $this->groupe_trick_id;
    }

    public function setGroupeTrickId(?GroupeTrick $groupe_trick_id): self
    {
        $this->groupe_trick_id = $groupe_trick_id;

        return $this;
    }

    /**
     * @return Collection<int, ImagesTrick>
     */
    public function getImagesTricks(): Collection
    {
        return $this->imagesTricks;
    }

    public function addImagesTrick(ImagesTrick $imagesTrick): self
    {
        if (!$this->imagesTricks->contains($imagesTrick)) {
            $this->imagesTricks->add($imagesTrick);
            $imagesTrick->setTrickId($this);
        }

        return $this;
    }

    public function removeImagesTrick(ImagesTrick $imagesTrick): self
    {
        if ($this->imagesTricks->removeElement($imagesTrick)) {
            // set the owning side to null (unless already changed)
            if ($imagesTrick->getTrickId() === $this) {
                $imagesTrick->setTrickId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VideosTrick>
     */
    public function getVideosTricks(): Collection
    {
        return $this->videosTricks;
    }

    public function addVideosTrick(VideosTrick $videosTrick): self
    {
        if (!$this->videosTricks->contains($videosTrick)) {
            $this->videosTricks->add($videosTrick);
            $videosTrick->setTrickId($this);
        }

        return $this;
    }

    public function removeVideosTrick(VideosTrick $videosTrick): self
    {
        if ($this->videosTricks->removeElement($videosTrick)) {
            // set the owning side to null (unless already changed)
            if ($videosTrick->getTrickId() === $this) {
                $videosTrick->setTrickId(null);
            }
        }

        return $this;
    }
}
