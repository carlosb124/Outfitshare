<?php

namespace App\Entity;

use App\Repository\SavedOutfitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SavedOutfitRepository::class)]
class SavedOutfit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'savedOutfits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'savedOutfits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Outfit $outfit = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $savedAt = null;

    public function __construct()
    {
        $this->savedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getOutfit(): ?Outfit
    {
        return $this->outfit;
    }

    public function setOutfit(?Outfit $outfit): static
    {
        $this->outfit = $outfit;

        return $this;
    }

    public function getSavedAt(): ?\DateTimeImmutable
    {
        return $this->savedAt;
    }

    public function setSavedAt(\DateTimeImmutable $savedAt): static
    {
        $this->savedAt = $savedAt;

        return $this;
    }
}