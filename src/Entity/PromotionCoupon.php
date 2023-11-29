<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromotionCouponRepository")
 */
class PromotionCoupon
{
    const TYPE_VALIDITY = 'date_valid';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $code;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $validFrom;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $validTo;

    /**
     * @ORM\Column(type="integer")
     */
    private int $discount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ShopOrder", mappedBy="coupon")
     */
    private Collection $shopOrders;

    /**
     * @ORM\OneToMany(targetEntity=PromotionOption::class, mappedBy="promotionId", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $promotionOptions;

    /**
     * @ORM\OneToMany(targetEntity=OrderProduct::class, mappedBy="promoCoupon")
     */
    private Collection $orderProducts;

    public function __construct()
    {
        $this->shopOrders = new ArrayCollection();
        $this->promotionOptions = new ArrayCollection();
        $this->orderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValidFrom(): ?\DateTimeInterface
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTimeInterface $validFrom): self
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function getValidTo(): ?\DateTimeInterface
    {
        return $this->validTo;
    }

    public function setValidTo(\DateTimeInterface $validTo): self
    {
        $this->validTo = $validTo;

        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return Collection|ShopOrder[]
     */
    public function getShopOrders(): Collection
    {
        return $this->shopOrders;
    }

    public function addShopOrder(ShopOrder $shopOrder): self
    {
        if (!$this->shopOrders->contains($shopOrder)) {
            $this->shopOrders[] = $shopOrder;
            $shopOrder->setCoupon($this);
        }

        return $this;
    }

    public function removeShopOrder(ShopOrder $shopOrder): self
    {
        if ($this->shopOrders->contains($shopOrder)) {
            $this->shopOrders->removeElement($shopOrder);
            // set the owning side to null (unless already changed)
            if ($shopOrder->getCoupon() === $this) {
                $shopOrder->setCoupon(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PromotionOption>
     */
    public function getPromotionOptions(): Collection
    {
        return $this->promotionOptions;
    }

    public function addPromotionOption(PromotionOption $promotionOption): self
    {
        if (!$this->promotionOptions->contains($promotionOption)) {
            $this->promotionOptions[] = $promotionOption;
            $promotionOption->setPromotionId($this);
        }

        return $this;
    }

    public function removePromotionOption(PromotionOption $promotionOption): self
    {
        if ($this->promotionOptions->removeElement($promotionOption)) {
            // set the owning side to null (unless already changed)
            if ($promotionOption->getPromotionId() === $this) {
                $promotionOption->setPromotionId(null);
            }
        }

        return $this;
    }

    public function getOptionTypes(): ?array
    {
        if (true === $this->promotionOptions->isEmpty()) {
            return null;
        }

        $types = [];

        foreach ($this->promotionOptions as $promotionOption) {
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
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProduct $orderProduct): self
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts[] = $orderProduct;
            $orderProduct->setPromoCoupon($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        if ($this->orderProducts->removeElement($orderProduct)) {
            // set the owning side to null (unless already changed)
            if ($orderProduct->getPromoCoupon() === $this) {
                $orderProduct->setPromoCoupon(null);
            }
        }

        return $this;
    }
}
