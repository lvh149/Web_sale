<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 * @UniqueEntity("email")
 */
class Users implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     * )
     *
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $roles;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     *  * @Assert\Length(
     *      min = 6,
     *      max = 250,
     *      minMessage = "Your password must be at least {{ limit }} characters long",
     *      maxMessage = "Your password cannot be longer than {{ limit }} characters"
     * )
     *
     */
    private $password;
    /**
     * @ORM\Column(type="string", length=255,unique = true)
     * @Assert\Length(min = 8, max = 20)
     * @Assert\Regex(pattern="/^[0-9]*$/")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $address;
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $point;
    /**
     * @ORM\OneToMany(targetEntity=Orders::class, mappedBy="user_id")
     */
    private $orders;

    /**
     * @SecurityAssert\UserPassword(groups={"registration"},
     *     message = "Wrong value for your current password!"
     * )
     */
    public $oldPassword;

    /**
     * @Assert\Length(
     *     min = 6,
     *     minMessage = "Password should be at least 6 chars long"
     * )
     */
    protected $newPassword;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToOne(targetEntity=Cart::class, mappedBy="customer", cascade={"persist", "remove"})
     */
    private $Cart;

    public const SUPER_ADMIN = 0;
    public const ADMIN = 1;
    public const CUSTOMER = 2;

    public static function Role(): array
    {
        $arrRole = [
            'ROLE_SUPERADMIN'  => self::SUPER_ADMIN,
            'ROLE_ADMIN'  => self::ADMIN,
            'ROLE_CUSTOMER'  => self::CUSTOMER,
        ];
        return $arrRole;
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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles()
    {
        foreach($this->Role() as $role => $id){
            if($this->roles == $id){
                $roles = $role;
            }
        }
        return array_unique([$roles]);
    }

    public function getRoleNumber()
    {
        return $this->roles;
    }

    public function nameRoles()
    {
        $roles = $this->roles;
        $role = $roles == 2 ? "Khách hàng" : ($roles == 1 ? "Nhân viên" : "Quản lý");

        return $role;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles[0];

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPoint(): ?int
    {
        if($this->point == null){
            $this->point = 0;
        }
        return $this->point;
    }

    public function addPoint(?int $point): self
    {
        $this->point = $this->getPoint();
        $this->point += $point;

        return $this;
    }
    public function setPoint(?int $point): self
    {
        $this->point = $point;

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setAdminId($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getAdminId() === $this) {
                $order->setAdminId(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->Cart;
    }

    public function setCart(Cart $Cart): self
    {
        // set the owning side of the relation if necessary
        if ($Cart->getCustomer() !== $this) {
            $Cart->setCustomer($this);
        }

        $this->Cart = $Cart;

        return $this;
    }
}
