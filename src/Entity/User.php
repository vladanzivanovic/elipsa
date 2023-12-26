<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;

    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DISABLED = 3;

    const LOGIN_TYPE_INTERN = 'intern';

    const LOGIN_TYPE_FACEBOOK = 'facebook';

    const LOGIN_TYPE_GOOGLE = 'google';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="field.required", groups={"SetUser", "SetUserAdmin"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string|null The hashed password
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(message="field.required", groups={"SetUser", "SetUserAdmin"})
     * @Assert\EqualTo(message="field.password_not_equal", propertyPath="rePassword", groups={"SetUser"})
     */
    private $password;

    /**
     * @var string|null The hashed password
     */
    private $rePassword;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $status = self::STATUS_PENDING;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="field.required", groups={"SetUser", "SetUserAdmin"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="field.required", groups={"SetUser", "SetUserAdmin"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $resetRequestAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShopOrder", mappedBy="user")
     */
    private $shopOrders;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Address", mappedBy="user", cascade={"persist", "remove"})
     */
    private $address;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserWishes", mappedBy="user", orphanRemoval=true)
     */
    private $userWishes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\NewsLetter", mappedBy="user")
     */
    private $newsLetters;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="user")
     */
    private $notifications;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $loginType = self::LOGIN_TYPE_INTERN;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $socialId;

    public function __construct()
    {
        $this->shopOrders = new ArrayCollection();
        $this->userWishes = new ArrayCollection();
        $this->newsLetters = new ArrayCollection();
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

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRePassword(): ?string
    {
        return $this->rePassword;
    }

    public function setRePassword(string $rePassword): self
    {
        $this->rePassword = $rePassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetRequestAt(): ?\DateTimeInterface
    {
        return $this->resetRequestAt;
    }

    public function setResetRequestAt(?\DateTimeInterface $resetRequestAt): self
    {
        $this->resetRequestAt = $resetRequestAt;

        return $this;
    }

    /**
     * @return Collection|ShopOrder[]
     */
    public function getShopOrders(): Collection
    {
        return $this->shopOrders;
    }

    public function addShopOrder(ShopOrder $shopOrder): self
    {
        if (!$this->shopOrders->contains($shopOrder)) {
            $this->shopOrders[] = $shopOrder;
            $shopOrder->setUser($this);
        }

        return $this;
    }

    public function removeShopOrder(ShopOrder $shopOrder): self
    {
        if ($this->shopOrders->contains($shopOrder)) {
            $this->shopOrders->removeElement($shopOrder);
            // set the owning side to null (unless already changed)
            if ($shopOrder->getUser() === $this) {
                $shopOrder->setUser(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $address ? null : $this;
        if ($address->getUser() !== $newUser) {
            $address->setUser($newUser);
        }

        return $this;
    }

    /**
     * @return Collection|UserWishes[]
     */
    public function getUserWishes(): Collection
    {
        return $this->userWishes;
    }

    public function addUserWish(UserWishes $userWish): self
    {
        if (!$this->userWishes->contains($userWish)) {
            $this->userWishes[] = $userWish;
            $userWish->setUser($this);
        }

        return $this;
    }

    public function removeUserWish(UserWishes $userWish): self
    {
        if ($this->userWishes->contains($userWish)) {
            $this->userWishes->removeElement($userWish);
            // set the owning side to null (unless already changed)
            if ($userWish->getUser() === $this) {
                $userWish->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|NewsLetter[]
     */
    public function getNewsLetters(): Collection
    {
        return $this->newsLetters;
    }

    public function addNewsLetter(NewsLetter $newsLetter): self
    {
        if (!$this->newsLetters->contains($newsLetter)) {
            $this->newsLetters[] = $newsLetter;
            $newsLetter->setUser($this);
        }

        return $this;
    }

    public function removeNewsLetter(NewsLetter $newsLetter): self
    {
        if ($this->newsLetters->contains($newsLetter)) {
            $this->newsLetters->removeElement($newsLetter);
            // set the owning side to null (unless already changed)
            if ($newsLetter->getUser() === $this) {
                $newsLetter->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getLoginType(): ?string
    {
        return $this->loginType;
    }

    public function setLoginType(string $loginType): self
    {
        $this->loginType = $loginType;

        return $this;
    }

    public function getSocialId(): ?string
    {
        return $this->socialId;
    }

    public function setSocialId(string $socialId): self
    {
        $this->socialId = $socialId;

        return $this;
    }
}
