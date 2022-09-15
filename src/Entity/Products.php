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

    /**
     * @ORM\OneToMany(targetEntity=OrdersDetail::class, mappedBy="product_id")
     */
    private $ordersDetails;

    /**
     * @ORM\OneToMany(targetEntity=ProductsParameter::class, mappedBy="product", orphanRemoval=true)
     */
    private $parameter;

    public function __construct()
    {
        $this->ordersDetails = new ArrayCollection();
        $this->parameter = new ArrayCollection();
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
     * @return Collection<int, ProductsParameter>
     */
    public function getParameter(): Collection
    {
        return $this->parameter;
    }

    public function addParameter(ProductsParameter $parameter): self
    {
        if (!$this->parameter->contains($parameter)) {
            $this->parameter[] = $parameter;
            $parameter->setProduct($this);
        }

        return $this;
    }

    public function removeParameter(ProductsParameter $parameter): self
    {
        if ($this->parameter->removeElement($parameter)) {
            // set the owning side to null (unless already changed)
            if ($parameter->getProduct() === $this) {
                $parameter->setProduct(null);
            }
        }

        return $this;
    }
}
