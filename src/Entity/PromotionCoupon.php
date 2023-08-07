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
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=10)
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
     * @ORM\Column(type="boolean")
     */
    private bool $useOnDiscountedProducts = false;

    public function __construct()
    {
        $this->shopOrders = new ArrayCollection();
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

    public function isUseOnDiscountedProducts(): ?bool
    {
        return $this->useOnDiscountedProducts;
    }

    public function setUseOnDiscountedProducts(bool $useOnDiscountedProducts): self
    {
        $this->useOnDiscountedProducts = $useOnDiscountedProducts;

        return $this;
    }
}
