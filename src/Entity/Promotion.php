<?php

namespace App\Entity;

use App\Entity\Resources\CountryResourceInterface;
use App\Entity\Resources\CountryResourceTrait;
use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\ResourceTrait;
use App\Entity\Resources\StatusInterface;
use App\Entity\Resources\StatusTrait;
use App\Repository\PromotionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PromotionRepository::class)]
#[ORM\Table(name: 'promotion')]
#[ORM\UniqueConstraint(name: 'promo_code', columns: ['code'])]
class Promotion implements EntityInterface, CountryResourceInterface, StatusInterface
{
    use ResourceTrait;
    use CountryResourceTrait;
    use StatusTrait;

    const CHECKER_TYPE_VALIDITY = 'date_valid';

    const TYPE_COUPON = 'coupon';

    const TYPE_PRODUCT = 'product';

    const TYPE_FREE_SHIPPING = 'free_shipping';

    #[ORM\Column(type: 'string')]
    private string $code;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $validFrom;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $validTo;

    #[ORM\Column(type: 'integer')]
    private int $discount;

    #[ORM\OneToMany(mappedBy: 'coupon', targetEntity: ShopOrder::class)]
    private Collection $shopOrders;

    /**
     * @var Collection<int, PromotionOption>
     */
    #[ORM\OneToMany(mappedBy: 'promotionId', targetEntity: PromotionOption::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $promotionOptions;

    #[ORM\OneToMany(mappedBy: 'promotion', targetEntity: OrderProduct::class)]
    private Collection $orderProducts;

    #[ORM\Column(type: 'string', length: 255)]
    private string $type;

    public function __construct()
    {
        $this->shopOrders = new ArrayCollection();
        $this->promotionOptions = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getValidFrom(): ?\DateTimeInterface
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTimeInterface $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(\DateTimeInterface $validTo): void
    {
        $this->validTo = $validTo;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): void
    {
        $this->discount = $discount;
    }

    /**
     * @return Collection<int, ShopOrder>
     */
    public function getShopOrders(): Collection
    {
        return $this->shopOrders;
    }

    public function addShopOrder(ShopOrder $shopOrder): void
    {
        if (!$this->shopOrders->contains($shopOrder)) {
            $this->shopOrders[] = $shopOrder;
            $shopOrder->setCoupon($this);
        }
    }

    public function removeShopOrder(ShopOrder $shopOrder): void
    {
        if ($this->shopOrders->contains($shopOrder)) {
            $this->shopOrders->removeElement($shopOrder);
            // set the owning side to null (unless already changed)
            if ($shopOrder->getCoupon() === $this) {
                $shopOrder->setCoupon(null);
            }
        }
    }

    /**
     * @return Collection<int, PromotionOption>
     */
    public function getPromotionOptions(): Collection
    {
        return $this->promotionOptions;
    }

    public function addPromotionOption(PromotionOption $promotionOption): void
    {
        if (!$this->promotionOptions->contains($promotionOption)) {
            $this->promotionOptions[] = $promotionOption;
            $promotionOption->setPromotionId($this);
        }
    }

    public function removePromotionOption(PromotionOption $promotionOption): void
    {
        $this->promotionOptions->removeElement($promotionOption);
    }

    /**
     * @return PromotionOption[]
     */
    public function getOptionTypes(): array
    {
        $types = [];

        foreach ($this->promotionOptions as $promotionOption) {
            if (PromotionOption::OPTION_ALL_PRODUCTS === $promotionOption->getType()) {
                continue;
            }

            $types[] = $promotionOption->getType();
        }

        return $types;
    }

    public function getOptionByType(string $type): ?PromotionOption
    {
        $filteredCollection = $this->promotionOptions->filter(function (PromotionOption $promotionOption) use ($type) : bool {
            return $promotionOption->getType() === $type;
        });

        return $filteredCollection->first() ?? null;
    }

    /**
     * @param string $type
     * @return Collection<int, PromotionOption>
     */
    public function getOptionsByType(string $type): Collection
    {
        $filteredCollection = $this->promotionOptions->filter(function (PromotionOption $promotionOption) use ($type) : bool {
            return $promotionOption->getType() === $type;
        });

        return $filteredCollection;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProduct $orderProduct): void
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts[] = $orderProduct;
        }
    }

    public function removeOrderProduct(OrderProduct $orderProduct): void
    {
        $this->orderProducts->removeElement($orderProduct);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function isPromotionInUse(): bool
    {
        return $this->shopOrders->count() > 0 || $this->orderProducts->count() > 0;
    }
}
