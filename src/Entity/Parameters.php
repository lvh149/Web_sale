<?php

namespace App\Entity;

use App\Repository\ParametersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ParametersRepository::class)
 */
class Parameters
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $value;

    /**
     * @ORM\OneToMany(targetEntity=ProductsParameter::class, mappedBy="parameter", orphanRemoval=true)
     */
    private $productsParameters;

    public function __construct()
    {
        $this->productsParameters = new ArrayCollection();
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
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

    /**
     * @return Collection<int, ProductsParameter>
     */
    public function getProductsParameters(): Collection
    {
        return $this->productsParameters;
    }

    public function addProductsParameter(ProductsParameter $productsParameter): self
    {
        if (!$this->productsParameters->contains($productsParameter)) {
            $this->productsParameters[] = $productsParameter;
            $productsParameter->setParameter($this);
        }

        return $this;
    }

    public function removeProductsParameter(ProductsParameter $productsParameter): self
    {
        if ($this->productsParameters->removeElement($productsParameter)) {
            // set the owning side to null (unless already changed)
            if ($productsParameter->getParameter() === $this) {
                $productsParameter->setParameter(null);
            }
        }

        return $this;
    }
}
