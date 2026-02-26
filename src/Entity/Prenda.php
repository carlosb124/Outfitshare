<?php

namespace App\Entity;

use App\Repository\PrendaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrendaRepository::class)]
class Prenda
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __toString(): string
    {
        return $this->nombre ?? 'Prenda #' . $this->id;
    }

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $marca = null;

    #[ORM\Column(length: 100)]
    private ?string $categoria = null;

    #[ORM\ManyToOne(inversedBy: 'prendas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $size = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $price = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $purchaseDate = null;

    #[ORM\Column(type: 'string', enumType: \App\Enum\SeasonEnum::class, nullable: true)]
    private ?\App\Enum\SeasonEnum $season = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $conditionState = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $fabricType = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $careInstructions = null;

    #[ORM\Column(type: 'json')]
    private array $smartTags = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $purchaseLink = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $noBgImage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    /**
     * Helper to return the full URL of the image, whether it's local or from Cloudinary
     */
    public function getImagenUrl(): ?string
    {
        if (!$this->imagen) {
            return null;
        }

        // Check if it's already a full HTTP URL (Cloudinary)
        if (str_starts_with($this->imagen, 'http://') || str_starts_with($this->imagen, 'https://')) {
            return $this->imagen;
        }

        // Fallback for legacy local images
        return 'uploads/images/' . $this->imagen;
    }

    public function setImagen(?string $imagen): static
    {
        $this->imagen = $imagen;
        return $this;
    }

    public function getMarca(): ?string
    {
        return $this->marca;
    }

    public function setMarca(?string $marca): static
    {
        $this->marca = $marca;
        return $this;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): static
    {
        $this->categoria = $categoria;
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

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(?\DateTimeInterface $purchaseDate): static
    {
        $this->purchaseDate = $purchaseDate;
        return $this;
    }

    public function getSeason(): ?\App\Enum\SeasonEnum
    {
        return $this->season;
    }

    public function setSeason(?\App\Enum\SeasonEnum $season): static
    {
        $this->season = $season;
        return $this;
    }

    public function getConditionState(): ?string
    {
        return $this->conditionState;
    }

    public function setConditionState(?string $conditionState): static
    {
        $this->conditionState = $conditionState;
        return $this;
    }

    public function getFabricType(): ?string
    {
        return $this->fabricType;
    }

    public function setFabricType(?string $fabricType): static
    {
        $this->fabricType = $fabricType;
        return $this;
    }

    public function getCareInstructions(): ?string
    {
        return $this->careInstructions;
    }

    public function setCareInstructions(?string $careInstructions): static
    {
        $this->careInstructions = $careInstructions;
        return $this;
    }

    public function getSmartTags(): array
    {
        return $this->smartTags;
    }

    public function setSmartTags(array $smartTags): static
    {
        $this->smartTags = $smartTags;
        return $this;
    }

    public function getPurchaseLink(): ?string
    {
        return $this->purchaseLink;
    }

    public function setPurchaseLink(?string $purchaseLink): static
    {
        $this->purchaseLink = $purchaseLink;
        return $this;
    }

    public function getNoBgImage(): ?string
    {
        return $this->noBgImage;
    }

    public function setNoBgImage(?string $noBgImage): static
    {
        $this->noBgImage = $noBgImage;
        return $this;
    }
}