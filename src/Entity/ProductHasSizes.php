<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductHasSizesRepository")
 */
class ProductHasSizes
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductSize", inversedBy="productHasSizes")
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productHasSizes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAvailable;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?ProductSize
    {
        return $this->size;
    }

    public function setSize(?ProductSize $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }
}
