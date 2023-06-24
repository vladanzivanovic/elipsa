<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderProductRepository")
 */
class OrderProduct
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ShopOrder", inversedBy="orderProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $orderId;

    /**
     * @ORM\Column(type="string", length=5)
     *
     * @Assert\NotBlank(message="product.size")
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ProductColor", inversedBy="orderProducts")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\NotBlank(message="product.color")
     */
    private $color;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="product.quantity")
     * @Assert\Positive(message="product.quantity_positive_number")
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderProductTranslation", mappedBy="orderProduct", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $orderProductTranslations;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="orderProducts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Image")
     * @ORM\JoinColumn(nullable=false)
     */
    private $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discount;

    public function __construct()
    {
        $this->orderProductTranslations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
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

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(?int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return OrderProductTranslation
     */
    public function getByLocale(string $locale): OrderProductTranslation
    {
        $filteredTrans = $this->orderProductTranslations->filter(function ($trans) use ($locale) {
            /** @var OrderProductTranslation $trans */
            return $trans->getLocale() === $locale;
        });

        return $filteredTrans->first();
    }
}
