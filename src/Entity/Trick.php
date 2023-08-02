<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Trick entity
 */
#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[UniqueEntity(fields: ['name'], message: 'This trick already exists.')]
#[UniqueEntity(fields: ['slug'], message: 'This trick already exists.')]
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
    private ?GroupTrick $groupTrick = null;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: ImagesTrick::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $imagesTricks;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: VideosTrick::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $videosTricks;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $modificationDate = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     *
     */
    public function __construct()
    {
        $this->imagesTricks = new ArrayCollection();
        $this->videosTricks = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return GroupTrick|null
     */
    public function getGroupTrick(): ?GroupTrick
    {
        return $this->groupTrick;
    }

    /**
     * @param GroupTrick|null $groupTrick
     * @return $this
     */
    public function setGroupTrick(?GroupTrick $groupTrick): self
    {
        $this->groupTrick = $groupTrick;

        return $this;
    }

    /**
     * @return Collection<int, ImagesTrick>
     */
    public function getImagesTricks(): Collection
    {
        return $this->imagesTricks;
    }

    /**
     * @param ImagesTrick $imagesTrick
     * @return $this
     */
    public function addImagesTrick(ImagesTrick $imagesTrick): self
    {
        if (!$this->imagesTricks->contains($imagesTrick)) {
            $this->imagesTricks->add($imagesTrick);
            $imagesTrick->setTrick($this);
        }

        return $this;
    }

    /**
     * @param ImagesTrick $imagesTrick
     * @return $this
     */
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

    /**
     * @param VideosTrick $videosTrick
     * @return $this
     */
    public function addVideosTrick(VideosTrick $videosTrick): self
    {
        if (!$this->videosTricks->contains($videosTrick)) {
            $this->videosTricks->add($videosTrick);
            $videosTrick->setTrick($this);
        }

        return $this;
    }

    /**
     * @param VideosTrick $videosTrick
     * @return $this
     */
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

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    /**
     * @param \DateTimeInterface $creationDate
     * @return $this
     */
    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getModificationDate(): ?\DateTimeInterface
    {
        return $this->modificationDate;
    }

    /**
     * @param \DateTimeInterface|null $modificationDate
     * @return $this
     */
    public function setModificationDate(?\DateTimeInterface $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     * @return $this
     */
    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTrick($this);
        }

        return $this;
    }

    /**
     * @param Comment $comment
     * @return $this
     */
    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}
