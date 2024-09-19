<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\PromotionEligibilityInterface;
use App\Entity\Resources\ResourceTrait;
use App\Repository\OrderProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderProductRepository::class)]
class OrderProduct implements EntityInterface, PromotionEligibilityInterface
{
    use ResourceTrait;

    #[ORM\ManyToOne(targetEntity: ShopOrder::class, inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private ShopOrder $orderId;

    
    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'product.size')]
    private string $size;

    
    #[ORM\ManyToOne(targetEntity: ProductColor::class, inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'product.color')]
    private ProductColor $color;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'product.quantity')]
    #[Assert\Positive(message: 'product.quantity_positive_number')]
    private int $quantity;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\OneToMany(mappedBy: 'orderProduct', targetEntity: OrderProductTranslation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $orderProductTranslations;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'orderProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column(type: 'string', length: 255)]
    private string $code;

    #[ORM\ManyToOne(targetEntity: Image::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Image $image;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $discount = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $promotionPrice = null;

    private int $total = 0;

    #[ORM\ManyToOne(targetEntity: Promotion::class, inversedBy: 'orderProducts')]
    private ?Promotion $promotion = null;

    public function __construct()
    {
        $this->orderProductTranslations = new ArrayCollection();
    }

    public function getOrderId(): ?ShopOrder
    {
        return $this->orderId;
    }

    public function setOrderId(?ShopOrder $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getColor(): ?ProductColor
    {
        return $this->color;
    }

    public function setColor(?ProductColor $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        $this->recalculateTotal();

        return $this;
    }

    /**
     * @return Collection|OrderProductTranslation[]
     */
    public function getOrderProductTranslations(): Collection
    {
        return $this->orderProductTranslations;
    }

    public function addOrderProductTranslation(OrderProductTranslation $orderProductTranslation): self
    {
        if (!$this->orderProductTranslations->contains($orderProductTranslation)) {
            $this->orderProductTranslations[] = $orderProductTranslation;
            $orderProductTranslation->setOrderProduct($this);
        }

        return $this;
    }

    public function removeOrderProductTranslation(OrderProductTranslation $orderProductTranslation): self
    {
        if ($this->orderProductTranslations->contains($orderProductTranslation)) {
            $this->orderProductTranslations->removeElement($orderProductTranslation);
            // set the owning side to null (unless already changed)
            if ($orderProductTranslation->getOrderProduct() === $this) {
                $orderProductTranslation->setOrderProduct(null);
            }
        }

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

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

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDiscount(): null|int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): self
    {
        $this->discount = $discount;

        $this->recalculateTotal();

        return $this;
    }

    public function getByLocale(string $locale): null|OrderProductTranslation
    {
        $filtered = $this->orderProductTranslations->filter(function ($trans) use ($locale) {
            /** @var OrderProductTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return 0 < $filtered->count() ? $filtered->first() : null;
    }

    public function getBySlug(string $slug): ?OrderProductTranslation
    {
        $filteredTrans = $this->orderProductTranslations->filter(
            function (OrderProductTranslation $trans) use ($slug) {
                return $trans->getSlug() === $slug;
            }
        );

        return false !== $filteredTrans->first() ? $filteredTrans->first() : null;
    }

    public function getPromotionPrice(): ?int
    {
        $price = 0 < $this->discount ? $this->discount : $this->price;

        if (null === $this->promotionPrice) {
            return null;
        }

        return $price + $this->promotionPrice;
    }

    public function setPromotionPrice(?int $promotionPrice): self
    {
        $this->promotionPrice = $promotionPrice;

        $this->recalculateTotal();

        return $this;
    }

    public function getTotal(): int
    {
        $this->total = $this->recalculateTotal();

        return $this->total;
    }

    public function getOriginalTotal(): int
    {
        return $this->recalculateTotal(false);
    }

    public function getRealPrice(): int
    {
        return 0 < $this->discount ? $this->discount : $this->price;
    }

    public function getPromotion(): ?Promotion
    {
        return $this->promotion;
    }

    public function setPromotion(?Promotion $promotion): self
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function isProductAvailable(): bool
    {
        $productOption = $this->product->getOptionsByCountry($this->orderId->getCountry());

        if (null === $productOption) {
            return false;
        }

        foreach ($productOption->getAvailableSizes() as $availableSize) {
            if (
                ($availableSize->getSize()->getSlug() === $this->size) &&
                ($this->quantity <= $availableSize->getQuantity())
            ) {
                return true;
            }
        }

        return false;
    }

    private function recalculateTotal(bool $considerPromotion = true): int
    {
        $price = 0 < $this->discount ? $this->discount : $this->price;

        if (true === $considerPromotion && null !== $this->promotionPrice) {
            $price = $price + $this->promotionPrice;
        }

        return $price * $this->quantity;
    }
}
