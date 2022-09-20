<?php

namespace App\Entity;

use App\Repository\OrdersDetailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OrdersDetailRepository::class)
 */
class OrdersDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Orders::class, inversedBy="ordersDetails")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity=Products::class, inversedBy="ordersDetails")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type("int")
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type("int")
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?Orders
    {
        return $this->order;
    }

    public function setOrderId(?Orders $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function getProductId(): ?Products
    {
        return $this->product;
    }

    public function setProductId(?Products $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }
}
