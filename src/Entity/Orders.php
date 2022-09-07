<?php

namespace App\Entity;

use App\Repository\OrdersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdersRepository::class)
 */
class Orders
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Admins::class, inversedBy="orders")
     */
    private $admin;

    /**
     * @ORM\ManyToOne(targetEntity=Customers::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

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

    public function getAdminId(): ?Admins
    {
        return $this->admin;
    }

    public function setAdminId(?Admins $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    public function getCustomerId(): ?Customers
    {
        return $this->customer;
    }

    public function setCustomerId(?Customers $customer): self
    {
        $this->customer = $customer;

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
