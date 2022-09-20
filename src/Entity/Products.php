<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductsRepository::class)
 */
class Products
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $info;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\Positive
     * @Assert\Type("int")
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive
     * @Assert\Type("int")
     */
    private $point;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive
     * @Assert\Type("int")
     */
    private $point_give;
    //
    /**
     * @ORM\OneToMany(targetEntity=OrdersDetail::class, mappedBy="product_id")
     */
    private $ordersDetails;

    /**
     * @ORM\ManyToMany(targetEntity=Parameters::class, mappedBy="products", fetch="EAGER")
     * @ORM\JoinTable(name="parameters_products")
     */
    private $parameters;

    /**
     * @ORM\OneToMany(targetEntity=CartDetail::class, mappedBy="product", orphanRemoval=true)
     */
    private $cartDetail;



    public function __construct()
    {
        $this->ordersDetails = new ArrayCollection();
        $this->parameters = new ArrayCollection();
        $this->cartDetail = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;

        return $this;
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(string $info): self
    {
        $this->info = $info;

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

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(?int $point): self
    {
        $this->point = $point;

        return $this;
    }

    public function getPointGive(): ?int
    {
        return $this->point_give;
    }

    public function setPointGive(?int $point_give): self
    {
        $this->point_give = $point_give;

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
            $ordersDetail->setProductId($this);
        }

        return $this;
    }

    public function removeOrdersDetail(OrdersDetail $ordersDetail): self
    {
        if ($this->ordersDetails->removeElement($ordersDetail)) {
            // set the owning side to null (unless already changed)
            if ($ordersDetail->getProductId() === $this) {
                $ordersDetail->setProductId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Parameters>
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }


    public function setParameters(ArrayCollection $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }



    public function addParameter(Parameters $parameter): self
    {
        if (!$this->parameters->contains($parameter)) {
            $this->parameters[] = $parameter;
            $parameter->addProducts($this);
        }

        return $this;
    }

    public function removeParameter(Parameters $parameter): self
    {
        if ($this->parameters->removeElement($parameter)) {
            $parameter->removeProducts($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, CartDetail>
     */
    public function getCartDetail(): Collection
    {
        return $this->cartDetail;
    }

    public function addCartDetail(CartDetail $cartDetail): self
    {
        if (!$this->cartDetail->contains($cartDetail)) {
            $this->cartDetail[] = $cartDetail;
            $cartDetail->setProduct($this);
        }

        return $this;
    }

    public function removeCartDetail(CartDetail $cartDetail): self
    {
        if ($this->cartDetail->removeElement($cartDetail)) {
            // set the owning side to null (unless already changed)
            if ($cartDetail->getProduct() === $this) {
                $cartDetail->setProduct(null);
            }
        }

        return $this;
    }
}
