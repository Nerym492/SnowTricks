<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'This trick already exists.')]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GroupTrick $group_trick = null;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: ImagesTrick::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $imagesTricks;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: VideosTrick::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $videosTricks;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creation_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $modification_date = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function __construct()
    {
        $this->imagesTricks = new ArrayCollection();
        $this->videosTricks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getGroupTrick(): ?GroupTrick
    {
        return $this->group_trick;
    }

    public function setGroupTrick(?GroupTrick $group_trick): self
    {
        $this->group_trick = $group_trick;

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
            $imagesTrick->setTrick($this);
        }

        return $this;
    }

    public function removeImagesTrick(ImagesTrick $imagesTrick): self
    {
        if ($this->imagesTricks->removeElement($imagesTrick)) {
            // set the owning side to null (unless already changed)
            if ($imagesTrick->getTrick() === $this) {
                $imagesTrick->setTrick(null);
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
            $videosTrick->setTrick($this);
        }

        return $this;
    }

    public function removeVideosTrick(VideosTrick $videosTrick): self
    {
        if ($this->videosTricks->removeElement($videosTrick)) {
            // set the owning side to null (unless already changed)
            if ($videosTrick->getTrick() === $this) {
                $videosTrick->setTrick(null);
            }
        }

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getModificationDate(): ?\DateTimeInterface
    {
        return $this->modification_date;
    }

    public function setModificationDate(?\DateTimeInterface $modification_date): self
    {
        $this->modification_date = $modification_date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
