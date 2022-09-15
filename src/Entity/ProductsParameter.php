<?php

namespace App\Entity;

use App\Repository\ProductsParameterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductsParameterRepository::class)
 */
class ProductsParameter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Products::class, inversedBy="parameter")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=Parameters::class, inversedBy="productsParameters")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parameter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Products
    {
        return $this->product;
    }

    public function setProduct(?Products $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getParameter(): ?Parameters
    {
        return $this->parameter;
    }

    public function setParameter(?Parameters $parameter): self
    {
        $this->parameter = $parameter;

        return $this;
    }
}
