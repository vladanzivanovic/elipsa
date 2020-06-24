<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_ARCHIVED = 3;

    const HOME_PAGE_UP = 2;
    const HOME_PAGE_DOWN = 1;

    use TimestampableEntity;

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
     * @ORM\OneToMany(targetEntity="App\Entity\ProductTranslation", mappedBy="product", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $productTranslations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductHasTags", mappedBy="product", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $productHasTags;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $badge;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductHasCategories", mappedBy="product", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $productHasCategories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductHasSizes", mappedBy="product", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $productHasSizes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductHasImages", mappedBy="product", cascade={"persist", "remove"})
     */
    private $productHasImages;

    /**
     * @ORM\Column(type="smallint")
     */
    private $showHomePage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductCleaning", mappedBy="product", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $productCleanings;

    public function __construct()
    {
        $this->productSizes = new ArrayCollection();
        $this->productTranslations = new ArrayCollection();
        $this->productHasTags = new ArrayCollection();
        $this->productHasColors = new ArrayCollection();
        $this->productHasCategories = new ArrayCollection();
        $this->productHasSizes = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->productHasImages = new ArrayCollection();
        $this->productCleanings = new ArrayCollection();
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

    /**
     * @return Collection|ProductHasImages[]
     */
    public function getProductHasImages(): Collection
    {
        return $this->productHasImages;
    }

    public function addProductHasImage(ProductHasImages $productHasImage): self
    {
        if (!$this->productHasImages->contains($productHasImage)) {
            $this->productHasImages[] = $productHasImage;
            $productHasImage->setProduct($this);
        }

        return $this;
    }

    public function removeProductHasImage(ProductHasImages $productHasImage): self
    {
        if ($this->productHasImages->contains($productHasImage)) {
            $this->productHasImages->removeElement($productHasImage);
            // set the owning side to null (unless already changed)
            if ($productHasImage->getProduct() === $this) {
                $productHasImage->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return ProductTranslation
     */
    public function getByLocale(string $locale): ProductTranslation
    {
        $filteredTrans = $this->productTranslations->filter(function ($trans) use ($locale) {
            /** @var ProductTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return $filteredTrans->first();
    }

    public function getShowHomePage(): ?int
    {
        return $this->showHomePage;
    }

    public function setShowHomePage(int $showHomePage): self
    {
        $this->showHomePage = $showHomePage;

        return $this;
    }

    /**
     * @return Collection|ProductCleaning[]
     */
    public function getProductCleanings(): Collection
    {
        return $this->productCleanings;
    }

    public function addProductCleaning(ProductCleaning $productCleaning): self
    {
        if (!$this->productCleanings->contains($productCleaning)) {
            $this->productCleanings[] = $productCleaning;
            $productCleaning->setProduct($this);
        }

        return $this;
    }

    public function removeProductCleaning(ProductCleaning $productCleaning): self
    {
        if ($this->productCleanings->contains($productCleaning)) {
            $this->productCleanings->removeElement($productCleaning);
            // set the owning side to null (unless already changed)
            if ($productCleaning->getProduct() === $this) {
                $productCleaning->setProduct(null);
            }
        }

        return $this;
    }
}
