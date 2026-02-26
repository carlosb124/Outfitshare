<?php

namespace App\Entity;

use App\Repository\OutfitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OutfitRepository::class)]
class Outfit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'outfit', targetEntity: Like::class, orphanRemoval: true)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'outfit', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'outfit', targetEntity: SavedOutfit::class, orphanRemoval: true)]
    private Collection $savedOutfits;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fechaPublicacion = null;

    #[ORM\ManyToOne(inversedBy: 'outfits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Prenda::class)]
    private Collection $prendas;

    #[ORM\Column(type: 'json')]
    private array $accessories = [];

    #[ORM\Column(type: 'string', enumType: \App\Enum\OutfitTypeEnum::class, options: ['default' => \App\Enum\OutfitTypeEnum::USER_GENERATED])]
    private \App\Enum\OutfitTypeEnum $type = \App\Enum\OutfitTypeEnum::USER_GENERATED;

    public function __construct()
    {
        $this->prendas = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->savedOutfits = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->titulo ?? 'Outfit #' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setOutfit($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // Limpiar referencia inversa
            if ($like->getOutfit() === $this) {
                $like->setOutfit(null);
            }
        }

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getFechaPublicacion(): ?\DateTimeInterface
    {
        return $this->fechaPublicacion;
    }

    public function setFechaPublicacion(\DateTimeInterface $fechaPublicacion): static
    {
        $this->fechaPublicacion = $fechaPublicacion;
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

    /**
     * @return Collection<int, Prenda>
     */
    public function getPrendas(): Collection
    {
        return $this->prendas;
    }

    public function addPrenda(Prenda $prenda): static
    {
        if (!$this->prendas->contains($prenda)) {
            $this->prendas->add($prenda);
        }

        return $this;
    }

    public function removePrenda(Prenda $prenda): static
    {
        $this->prendas->removeElement($prenda);

        return $this;
    }

    public function getAccessories(): array
    {
        return $this->accessories;
    }

    public function setAccessories(array $accessories): static
    {
        $this->accessories = $accessories;
        return $this;
    }

    public function getType(): \App\Enum\OutfitTypeEnum
    {
        return $this->type;
    }

    public function setType(\App\Enum\OutfitTypeEnum $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setOutfit($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // Limpiar referencia inversa
            if ($comment->getOutfit() === $this) {
                $comment->setOutfit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SavedOutfit>
     */
    public function getSavedOutfits(): Collection
    {
        return $this->savedOutfits;
    }

    public function addSavedOutfit(SavedOutfit $savedOutfit): static
    {
        if (!$this->savedOutfits->contains($savedOutfit)) {
            $this->savedOutfits->add($savedOutfit);
            $savedOutfit->setOutfit($this);
        }
        return $this;
    }

    public function removeSavedOutfit(SavedOutfit $savedOutfit): static
    {
        if ($this->savedOutfits->removeElement($savedOutfit)) {
            if ($savedOutfit->getOutfit() === $this) {
                $savedOutfit->setOutfit(null);
            }
        }
        return $this;
    }
}