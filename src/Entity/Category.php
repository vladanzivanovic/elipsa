<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SiteBundle\Entity\BlogTranslation;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="children")
     */
    private $parent;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CategoryTranslation", mappedBy="category", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $categoryTranslations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Product", mappedBy="category")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductHasCategories", mappedBy="category")
     */
    private $productHasCategories;

    public function __construct()
    {
        $this->categoryTranslations = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->productHasCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $category): self
    {
        $this->parent = $category;

        return $this;
    }

    /**
     * @return Collection|CategoryTranslation[]
     */
    public function getCategoryTranslations(): Collection
    {
        return $this->categoryTranslations;
    }

    public function addCategoryTranslation(CategoryTranslation $categoryTranslation): self
    {
        if (!$this->categoryTranslations->contains($categoryTranslation)) {
            $this->categoryTranslations[] = $categoryTranslation;
            $categoryTranslation->setCategory($this);
        }

        return $this;
    }

    public function removeCategoryTranslation(CategoryTranslation $categoryTranslation): self
    {
        if ($this->categoryTranslations->contains($categoryTranslation)) {
            $this->categoryTranslations->removeElement($categoryTranslation);
            // set the owning side to null (unless already changed)
            if ($categoryTranslation->getCategory() === $this) {
                $categoryTranslation->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return ArrayCollection
     */
    public function getTranslationByLocale(string $locale): ArrayCollection
    {
        return $this->categoryTranslations->filter(function ($item) use ($locale) {
            /** @var CategoryTranslation $item */
            return $item->getLocale() == $locale;
        });
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

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
            $productHasCategory->setCategory($this);
        }

        return $this;
    }

    public function removeProductHasCategory(ProductHasCategories $productHasCategory): self
    {
        if ($this->productHasCategories->contains($productHasCategory)) {
            $this->productHasCategories->removeElement($productHasCategory);
            // set the owning side to null (unless already changed)
            if ($productHasCategory->getCategory() === $this) {
                $productHasCategory->setCategory(null);
            }
        }

        return $this;
    }
}
