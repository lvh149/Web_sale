<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Timestampable\Traits\TimestampableEntity;
// use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=OrdersRepository::class)
 * * @ORM\HasLifecycleCallbacks()
 */
class Orders
{
    use TimestampableEntity;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="orders")
     */
    private $admin;

    /**
     * @ORM\ManyToOne(targetEntity=Users::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $customer;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date
     */
    private $date;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank
     */
    private $status;


    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type("int")
     */
    private $total_price;

    /**
     * @ORM\OneToMany(targetEntity=OrdersDetail::class, mappedBy="order_id")
     */
    private $ordersDetails;


    public function __construct()
    {
        $this->ordersDetails = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdminId(): ?Users
    {
        return $this->admin;
    }

    public function setAdminId(?Users $user): self
    {
        $this->admin = $user;

        return $this;
    }

    public function getCustomerId(): ?Users
    {
        return $this->customer;
    }

    public function setCustomerId(?Users $user): self
    {
        $this->customer = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
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
    public function getTotalPrice(): ?int
    {
        return $this->total_price;
    }

    public function setTotalPrice(int $total_price): self
    {
        $this->total_price = $total_price;

        return $this;
    }

    /**
     * @return Collection<int, OrdersDetail>
     */
    public function getOrdersDetails(): Collection
    {
        return $this->ordersDetails;
    }

    public function addOrdersDetail(OrdersDetail $ordersDetail): self
    {
        if (!$this->ordersDetails->contains($ordersDetail)) {
            $this->ordersDetails[] = $ordersDetail;
            $ordersDetail->setOrderId($this);
        }

        return $this;
    }

    public function removeOrdersDetail(OrdersDetail $ordersDetail): self
    {
        if ($this->ordersDetails->removeElement($ordersDetail)) {
            // set the owning side to null (unless already changed)
            if ($ordersDetail->getOrderId() === $this) {
                $ordersDetail->setOrderId(null);
            }
        }

        return $this;
    }
}
