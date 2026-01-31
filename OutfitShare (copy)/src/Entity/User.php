<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Enum\StylePreferenceEnum;

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

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SavedOutfit::class, orphanRemoval: true)]
    private Collection $savedOutfits;

    #[ORM\Column(type: 'boolean')]
    private bool $isBanned = false;

    // --- New Fields ---

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $googleId = null;

    #[ORM\Column(length: 50, unique: true, nullable: true)]
    private ?string $nickname = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(type: 'string', enumType: StylePreferenceEnum::class, nullable: true)]
    private ?StylePreferenceEnum $stylePreference = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $biography = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $profilePhoto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bannerPhoto = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isPublic = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $showLikesPublicly = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $showSavedPublicly = false;

    #[ORM\Column(type: 'json')]
    private array $personalityTraits = [];

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'following')]
    private Collection $followers;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'followers')]
    private Collection $following;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'recipient', orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $notifications;

    public function __construct()
    {
        $this->prendas = new ArrayCollection();
        $this->prendas = new ArrayCollection();
        $this->outfits = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->savedOutfits = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->notifications = new ArrayCollection();
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
            $savedOutfit->setUser($this);
        }

        return $this;
    }

    public function removeSavedOutfit(SavedOutfit $savedOutfit): static
    {
        if ($this->savedOutfits->removeElement($savedOutfit)) {
            // set the owning side to null (unless already changed)
            if ($savedOutfit->getUser() === $this) {
                $savedOutfit->setUser(null);
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

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): static
    {
        $this->googleId = $googleId;
        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): static
    {
        $this->nickname = $nickname;
        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;
        return $this;
    }

    public function getStylePreference(): ?StylePreferenceEnum
    {
        return $this->stylePreference;
    }

    public function setStylePreference(?StylePreferenceEnum $stylePreference): static
    {
        $this->stylePreference = $stylePreference;
        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): static
    {
        $this->biography = $biography;
        return $this;
    }

    public function getProfilePhoto(): ?string
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto(?string $profilePhoto): static
    {
        $this->profilePhoto = $profilePhoto;
        return $this;
    }

    public function getBannerPhoto(): ?string
    {
        return $this->bannerPhoto;
    }

    public function setBannerPhoto(?string $bannerPhoto): static
    {
        $this->bannerPhoto = $bannerPhoto;
        return $this;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function isShowLikesPublicly(): bool
    {
        return $this->showLikesPublicly;
    }

    public function setShowLikesPublicly(bool $showLikesPublicly): static
    {
        $this->showLikesPublicly = $showLikesPublicly;
        return $this;
    }

    public function isShowSavedPublicly(): bool
    {
        return $this->showSavedPublicly;
    }

    public function setShowSavedPublicly(bool $showSavedPublicly): static
    {
        $this->showSavedPublicly = $showSavedPublicly;
        return $this;
    }

    public function getPersonalityTraits(): array
    {
        return $this->personalityTraits;
    }

    public function setPersonalityTraits(array $personalityTraits): static
    {
        $this->personalityTraits = $personalityTraits;
        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(self $follower): static
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
            $follower->addFollowing($this);
        }

        return $this;
    }

    public function removeFollower(self $follower): static
    {
        if ($this->followers->removeElement($follower)) {
            $follower->removeFollowing($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowing(): Collection
    {
        return $this->following;
    }

    public function addFollowing(self $following): static
    {
        if (!$this->following->contains($following)) {
            $this->following->add($following);
        }

        return $this;
    }

    public function removeFollowing(self $following): static
    {
        $this->following->removeElement($following);

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setRecipient($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getRecipient() === $this) {
                $notification->setRecipient(null);
            }
        }

        return $this;
    }
}
