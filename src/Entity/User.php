<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Prenda::class, orphanRemoval: true)]
    private Collection $prendas;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Outfit::class, orphanRemoval: true)]
    private Collection $outfits;

    #[ORM\Column(options: ['default' => 0])]
    private ?int $puntos = 0;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Like::class, orphanRemoval: true)]
    private Collection $likes;

    #[ORM\Column(type: 'boolean')]
    private bool $isBanned = false;

    public function __construct()
    {
        $this->prendas = new ArrayCollection();
        $this->outfits = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getPrendas(): Collection
    {
        return $this->prendas;
    }

    public function addPrenda(Prenda $prenda): static
    {
        if (!$this->prendas->contains($prenda)) {
            $this->prendas->add($prenda);
            $prenda->setUser($this);
        }
        return $this;
    }

    public function removePrenda(Prenda $prenda): static
    {
        if ($this->prendas->removeElement($prenda)) {
            if ($prenda->getUser() === $this) {
                $prenda->setUser(null);
            }
        }
        return $this;
    }

    public function getOutfits(): Collection
    {
        return $this->outfits;
    }

    public function addOutfit(Outfit $outfit): static
    {
        if (!$this->outfits->contains($outfit)) {
            $this->outfits->add($outfit);
            $outfit->setUser($this);
        }
        return $this;
    }

    public function removeOutfit(Outfit $outfit): static
    {
        if ($this->outfits->removeElement($outfit)) {
            if ($outfit->getUser() === $this) {
                $outfit->setUser(null);
            }
        }
        return $this;
    }

    public function getPuntos(): ?int
    {
        return $this->puntos;
    }

    public function setPuntos(int $puntos): static
    {
        $this->puntos = $puntos;
        return $this;
    }

    public function getImage(): ?string
    {
        // Placeholder por si queremos añadir avatar real más tarde.
        // Por ahora usamos UI Avatars en el frontend.
        return null;
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
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    public function isBanned(): bool
    {
        return $this->isBanned;
    }

    public function setBanned(bool $isBanned): static
    {
        $this->isBanned = $isBanned;
        return $this;
    }
}
