<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DISABLED = 3;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discount;

    /**
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductSize", mappedBy="product", orphanRemoval=true)
     */
    private $productSizes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductTranslation", mappedBy="product", orphanRemoval=true)
     */
    private $productTranslations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductHasTags", mappedBy="product", orphanRemoval=true)
     */
    private $productHasTags;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $badge;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductHasColor", mappedBy="product", orphanRemoval=true)
     */
    private $productHasColors;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductHasCategories", mappedBy="product", orphanRemoval=true)
     */
    private $productHasCategories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductHasSizes", mappedBy="product", orphanRemoval=true)
     */
    private $productHasSizes;

    public function __construct()
    {
        $this->productSizes = new ArrayCollection();
        $this->productTranslations = new ArrayCollection();
        $this->productHasTags = new ArrayCollection();
        $this->productHasColors = new ArrayCollection();
        $this->productHasCategories = new ArrayCollection();
        $this->productHasSizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): self
    {
        $this->discount = $discount;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|ProductSize[]
     */
    public function getProductSizes(): Collection
    {
        return $this->productSizes;
    }

    public function addProductSize(ProductSize $productSize): self
    {
        if (!$this->productSizes->contains($productSize)) {
            $this->productSizes[] = $productSize;
            $productSize->setProduct($this);
        }

        return $this;
    }

    public function removeProductSize(ProductSize $productSize): self
    {
        if ($this->productSizes->contains($productSize)) {
            $this->productSizes->removeElement($productSize);
            // set the owning side to null (unless already changed)
            if ($productSize->getProduct() === $this) {
                $productSize->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductTranslation[]
     */
    public function getProductTranslations(): Collection
    {
        return $this->productTranslations;
    }

    public function addProductTranslation(ProductTranslation $productTranslation): self
    {
        if (!$this->productTranslations->contains($productTranslation)) {
            $this->productTranslations[] = $productTranslation;
            $productTranslation->setProduct($this);
        }

        return $this;
    }

    public function removeProductTranslation(ProductTranslation $productTranslation): self
    {
        if ($this->productTranslations->contains($productTranslation)) {
            $this->productTranslations->removeElement($productTranslation);
            // set the owning side to null (unless already changed)
            if ($productTranslation->getProduct() === $this) {
                $productTranslation->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductHasTags[]
     */
    public function getProductHasTags(): Collection
    {
        return $this->productHasTags;
    }

    public function addProductHasTag(ProductHasTags $productHasTag): self
    {
        if (!$this->productHasTags->contains($productHasTag)) {
            $this->productHasTags[] = $productHasTag;
            $productHasTag->setProduct($this);
        }

        return $this;
    }

    public function removeProductHasTag(ProductHasTags $productHasTag): self
    {
        if ($this->productHasTags->contains($productHasTag)) {
            $this->productHasTags->removeElement($productHasTag);
            // set the owning side to null (unless already changed)
            if ($productHasTag->getProduct() === $this) {
                $productHasTag->setProduct(null);
            }
        }

        return $this;
    }

    public function getBadge(): ?int
    {
        return $this->badge;
    }

    public function setBadge(?int $badge): self
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * @return Collection|ProductHasColor[]
     */
    public function getProductHasColors(): Collection
    {
        return $this->productHasColors;
    }

    public function addProductHasColor(ProductHasColor $productHasColor): self
    {
        if (!$this->productHasColors->contains($productHasColor)) {
            $this->productHasColors[] = $productHasColor;
            $productHasColor->setProduct($this);
        }

        return $this;
    }

    public function removeProductHasColor(ProductHasColor $productHasColor): self
    {
        if ($this->productHasColors->contains($productHasColor)) {
            $this->productHasColors->removeElement($productHasColor);
            // set the owning side to null (unless already changed)
            if ($productHasColor->getProduct() === $this) {
                $productHasColor->setProduct(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection|ProductHasCategories[]
     */
    public function getProductHasCategories(): Collection
    {
        return $this->productHasCategories;
    }

    public function addProductHasCategory(ProductHasCategories $productHasCategory): self
    {
        if (!$this->productHasCategories->contains($productHasCategory)) {
            $this->productHasCategories[] = $productHasCategory;
            $productHasCategory->setProduct($this);
        }

        return $this;
    }

    public function removeProductHasCategory(ProductHasCategories $productHasCategory): self
    {
        if ($this->productHasCategories->contains($productHasCategory)) {
            $this->productHasCategories->removeElement($productHasCategory);
            // set the owning side to null (unless already changed)
            if ($productHasCategory->getProduct() === $this) {
                $productHasCategory->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProductHasSizes[]
     */
    public function getProductHasSizes(): Collection
    {
        return $this->productHasSizes;
    }

    public function addProductHasSize(ProductHasSizes $productHasSize): self
    {
        if (!$this->productHasSizes->contains($productHasSize)) {
            $this->productHasSizes[] = $productHasSize;
            $productHasSize->setProduct($this);
        }

        return $this;
    }

    public function removeProductHasSize(ProductHasSizes $productHasSize): self
    {
        if ($this->productHasSizes->contains($productHasSize)) {
            $this->productHasSizes->removeElement($productHasSize);
            // set the owning side to null (unless already changed)
            if ($productHasSize->getProduct() === $this) {
                $productHasSize->setProduct(null);
            }
        }

        return $this;
    }
}
