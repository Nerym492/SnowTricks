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
    private ?GroupeTrick $groupe_trick = null;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: ImagesTrick::class, orphanRemoval: true)]
    private Collection $imagesTricks;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: VideosTrick::class, orphanRemoval: true)]
    private Collection $videosTricks;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

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

    public function getGroupeTrick(): ?GroupeTrick
    {
        return $this->groupe_trick;
    }

    public function setGroupeTrick(?GroupeTrick $groupe_trick): self
    {
        $this->groupe_trick = $groupe_trick;

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

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }
}
