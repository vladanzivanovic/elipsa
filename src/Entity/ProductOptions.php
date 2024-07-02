<?php

namespace App\Entity;

use App\Repository\ProductOptionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductOptionsRepository::class)]
class ProductOptions
{
    const HOME_PAGE_UP = 'up';
    const HOME_PAGE_DOWN = 'down';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $showHomePage = null;

    #[ORM\Column]
    private bool $sold = false;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    private ?int $discount = null;

    #[ORM\ManyToOne(inversedBy: 'productOptions')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column(length: 255)]
    private null|string $country = null;

    #[ORM\OneToMany(mappedBy: 'productOption', targetEntity: ProductHasSizes::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $productHasSizes;

    private null|int $promoDiscount = null;

    public function __construct()
    {
        $this->productHasSizes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShowHomePage(): null|string
    {
        return $this->showHomePage;
    }

    public function setShowHomePage(null|string $showHomePage): static
    {
        $this->showHomePage = $showHomePage;

        return $this;
    }

    public function isSold(): bool
    {
        return $this->sold;
    }

    public function setSold(bool $sold): static
    {
        $this->sold = $sold;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDiscount(): null|int
    {
        return $this->discount;
    }

    public function setDiscount(null|int $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, ProductHasSizes>
     */
    public function getProductHasSizes(): Collection
    {
        return $this->productHasSizes;
    }

    public function addProductHasSize(ProductHasSizes $productHasSize): static
    {
        if (!$this->productHasSizes->contains($productHasSize)) {
            $this->productHasSizes->add($productHasSize);
            $productHasSize->setProductOption($this);
        }

        return $this;
    }

    public function removeProductHasSize(ProductHasSizes $productHasSize): static
    {
        if ($this->productHasSizes->removeElement($productHasSize)) {
            // set the owning side to null (unless already changed)
            if ($productHasSize->getProductOption() === $this) {
                $productHasSize->setProductOption(null);
            }
        }

        return $this;
    }

    /**
     * @return array<int, ProductHasSizes>
     */
    public function getAvailableSizes(): array
    {
        $sizes = [];

        foreach ($this->productHasSizes as $productHasSize) {
            if (0 === $productHasSize->getQuantity()) {
                continue;
            }

            $sizes[$productHasSize->getSize()->getSize()] = $productHasSize;
        }

        ksort($sizes);

        return $sizes;
    }

    public function isSizeAvailable(string $sizeSlug): bool
    {
        foreach ($this->getAvailableSizes() as $availableSize) {
            if ($availableSize->getSize()->getSlug() === $sizeSlug) {
                return true;
            }
        }

        return false;
    }

    public function getPromoDiscount(): null|int
    {
        return $this->promoDiscount;
    }

    public function setPromoDiscount(null|int $promoDiscount): void
    {
        $this->promoDiscount = $promoDiscount;
    }
}
