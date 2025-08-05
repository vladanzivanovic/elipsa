<?php

namespace App\Entity;

use App\Entity\Resources\CountryResourceInterface;
use App\Entity\Resources\CountryResourceTrait;
use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\PromotionEligibilityInterface;
use App\Entity\Resources\ResourceTrait;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product implements EntityInterface, PromotionEligibilityInterface, CountryResourceInterface
{
    use ResourceTrait;
    use TimestampableEntity;
    use CountryResourceTrait;

    const STATUS_PENDING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_ARCHIVED = 3;

    const HOME_PAGE_UP = 2;
    const HOME_PAGE_DOWN = 1;

    #[ORM\Column(type: 'smallint')]
    private ?int $status;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductTranslation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $productTranslations;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductHasTags::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $productHasTags;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private ?int $badge;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $code;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductHasCategories::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $productHasCategories;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductHasImages::class, cascade: ['persist', 'remove'])]
    private Collection $productHasImages;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductCleaning::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $productCleanings;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Youtube::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $youtubes;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderProduct::class)]
    private Collection $orderProducts;

    /**
     * @var Collection<int, UserWishes>
     */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: UserWishes::class)]
    private Collection $userWishes;

    private ?int $promoDiscount = null;

    private bool $freeShippingEnabled = false;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductOptions::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $productOptions;

    public function __construct()
    {
        $this->productTranslations = new ArrayCollection();
        $this->productHasTags = new ArrayCollection();
        $this->productHasCategories = new ArrayCollection();
        $this->productHasImages = new ArrayCollection();
        $this->productCleanings = new ArrayCollection();
        $this->youtubes = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
        $this->userWishes = new ArrayCollection();
        $this->productOptions = new ArrayCollection();
    }

    public function getPrice(string $countryCode): ?int
    {
        $promotionOptions = $this->getOptionByCountry($countryCode);

        return $promotionOptions->getPrice();
    }

    public function getDiscount(string $countryCode): ?int
    {
        $promotionOptions = $this->getOptionByCountry($countryCode);

        return $promotionOptions->getDiscount();
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
     * @return array<int, Category>
     */
    public function getCategories(): array
    {
        $categories = [];

        foreach ($this->getProductHasCategories() as $productHasCategory) {
            $categories[] = $productHasCategory->getCategory();
        }

        return $categories;
    }

    /**
     * @return Collection<int, ProductHasImages>
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

    public function getMainImage(): null|Image
    {
        foreach ($this->getProductHasImages() as $productHasImage) {
            $image = $productHasImage->getImage();

            if (true === $image->getIsMain()) {
                return $image;
            }
        }

        return null;
    }

    public function getByLocale(string $locale): null|ProductTranslation
    {
        $filteredTrans = $this->productTranslations->filter(function ($trans) use ($locale) {
            /** @var ProductTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return false !== $filteredTrans->first() ? $filteredTrans->first() : null;
    }

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

    public function addYoutube(Youtube $youtube): void
    {
        if (!$this->youtubes->contains($youtube)) {
            $this->youtubes[] = $youtube;

            $youtube->setProduct($this);
        }
    }

    public function removeYoutubes(Youtube $youtube)
    {
        $this->youtubes->removeElement($youtube);
    }

    public function getYoutubes(): Collection
    {
        return $this->youtubes;
    }

    /**
     * @return array<int, ProductColor>
     */
    public function getProductColors(): array
    {
        $colors = [];

        foreach ($this->getProductHasImages() as $productHasImage) {
            $color = $productHasImage->getColor();

            if (isset($colors[$color->getHex()])) {
                continue;
            }

            $colors[$color->getHex()] = $color;
        }

        return $colors;
    }

    public function hasColor(ProductColor $color): bool
    {
        $colors = $this->getProductColors();

        return in_array($color, $colors, true);
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

//    public function isSold(): ?bool
//    {
//        return $this->sold;
//    }
//
//    public function setSold(bool $sold): self
//    {
//        $this->sold = $sold;
//
//        return $this;
//    }

    public function isUserWish(User $user): bool
    {
        foreach ($this->userWishes as $userWish) {
            if ($userWish->getUser() === $user) {
                return true;
            }
        }

        return false;
    }

    public function getPromoDiscount(string $countryCode): null|int
    {
        $productOptions = $this->getOptionByCountry($countryCode);

        return $productOptions->getPromoDiscount();
    }

    public function setPromoDiscount(null|int $promoDiscount, string $countryCode): void
    {
        $productOptions = $this->getOptionByCountry($countryCode);

        $productOptions->setPromoDiscount($promoDiscount);
    }

    /**
     * @return Collection<int, ProductOptions>
     */
    public function getProductOptions(): Collection
    {
        return $this->productOptions;
    }

    public function addProductOption(ProductOptions $productOption): static
    {
        if (!$this->productOptions->contains($productOption)) {
            $this->productOptions->add($productOption);
            $productOption->setProduct($this);
        }

        return $this;
    }

    public function removeProductOption(ProductOptions $productOption): static
    {
        if ($this->productOptions->removeElement($productOption)) {
            // set the owning side to null (unless already changed)
            if ($productOption->getProduct() === $this) {
                $productOption->setProduct(null);
            }
        }

        return $this;
    }

    public function getOptionByCountry(string $countryCode): null|ProductOptions
    {
        $filteredOptions = $this->productOptions->filter(function ($options) use ($countryCode) {
            /** @var ProductOptions $options */
            return $options->getCountry() === $countryCode;
        });

        return false !== $filteredOptions->first() ? $filteredOptions->first() : null;
    }

    public function getTagsByCountry(string $countryCode): null|Collection
    {
        $filteredTags = $this->productHasTags->filter(function ($hasTags) use ($countryCode) {
            /** @var ProductHasTags $hasTags */
            return $hasTags->getLocale() === $countryCode;
        });

        return false === $filteredTags->isEmpty() ? $filteredTags : null;
    }

    public function isFreeShippingEnabled(): bool
    {
        return $this->freeShippingEnabled;
    }

    public function setIsFreeShippingEnabled(bool $freeShippingEnabled): void
    {
        $this->freeShippingEnabled = $freeShippingEnabled;
    }

    public function isProductHasTag(Tags $tag): bool
    {
        $filteredOptions = $this->productHasTags->filter(function ($hasTags) use ($tag) {
            /** @var ProductHasTags $hasTags */
            return $hasTags->getTag() === $tag;
        });

        return $filteredOptions->count() > 0;
    }
}
