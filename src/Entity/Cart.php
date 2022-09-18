<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Users::class, inversedBy="cart", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity=CartDetail::class, mappedBy="cart", orphanRemoval=true)
     */
    private $cartDetail;

    public function __construct()
    {
        $this->cartDetail = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Users
    {
        return $this->customer;
    }

    public function setCustomer(Users $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, CartDetail>
     */
    public function getcartDetail(): Collection
    {
        return $this->cartDetail;
    }

    public function addcartDetail(CartDetail $cartDetail): self
    {
        if (!$this->cartDetail->contains($cartDetail)) {
            $this->cartDetail[] = $cartDetail;
            $cartDetail->setCart($this);
        }

        return $this;
    }

    public function removecartDetail(CartDetail $cartDetail): self
    {
        if ($this->cartDetail->removeElement($cartDetail)) {
            // set the owning side to null (unless already changed)
            if ($cartDetail->getCart() === $this) {
                $cartDetail->setCart(null);
            }
        }

        return $this;
    }
}
