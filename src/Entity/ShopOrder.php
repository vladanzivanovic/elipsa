<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShopOrderRepository")
 */
class ShopOrder
{
    public const STATUS_NEW = 1;
    public const STATUS_COMPLETED = 2;
    public const STATUS_AWAITING_AUTHORIZATION = 4;
    public const STATUS_REFUND = 5;
    public const STATUS_VOID = 6;
    public const STATUS_FAILED = 3;

    public const PAYMENT_TYPE_ON_DELIVERING = 1;
    public const PAYMENT_TYPE_CREDIT_CARD = 2;

    public const CARD_TYPE_PRE_AUTH = 'PreAuth';
    public const CARD_TYPE_POST_AUTH = 'PostAuth';
    public const CARD_TYPE_REFUND = 'Credit';
    public const CARD_TYPE_VOID = 'Void';

    public const CART_TYPE_REJECT = 'Reject';

    public const CARD_STATUS_MAPPER = [
        self::CARD_TYPE_POST_AUTH => self::STATUS_COMPLETED,
        self::CARD_TYPE_REFUND => self::STATUS_REFUND,
        self::CARD_TYPE_VOID => self::STATUS_VOID,
    ];

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $completedAt = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade={"persist", "remove"})
     */
    private ?Address $billingAddress = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Address", cascade={"persist", "remove"})
     */
    private ?Address $shippingAddress = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\OrderProduct", mappedBy="orderId", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var Collection<int, OrderProduct>
     */
    private Collection $orderProducts;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="shopOrders", cascade={"persist", "remove"})
     */
    private ?User $user = null;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private ?int $paymentType = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $note = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PromotionCoupon", inversedBy="shopOrders")
     */
    private ?PromotionCoupon $coupon = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private array $transactionData = [];

    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    private string $token;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeInterface $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?Address $billingAddress): self
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?Address $shippingAddress): self
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * @return Collection|OrderProduct[]
     */
    public function getOrderProducts(): Collection
    {
        return $this->orderProducts;
    }

    public function addOrderProduct(OrderProduct $orderProduct): self
    {
        if (!$this->orderProducts->contains($orderProduct)) {
            $this->orderProducts[] = $orderProduct;
            $orderProduct->setOrderId($this);
        }

        return $this;
    }

    public function removeOrderProduct(OrderProduct $orderProduct): self
    {
        if ($this->orderProducts->contains($orderProduct)) {
            $this->orderProducts->removeElement($orderProduct);
            // set the owning side to null (unless already changed)
            if ($orderProduct->getOrderId() === $this) {
                $orderProduct->setOrderId(null);
            }
        }

        return $this;
    }

    public function getOrderProductByValues(
        string $slug,
        string $size,
        ProductColor $color
    ): ?OrderProduct {
        /** @var OrderProduct $orderProduct */
        foreach ($this->orderProducts as $orderProduct) {
            $trans = $orderProduct->getBySlug($slug);

            if (
                null !== $trans &&
                $size === $orderProduct->getSize() &&
                $color === $orderProduct->getColor()
            ) {
                return $orderProduct;
            }
        }

        return null;
    }

    public function getOrderProductById(int $orderProductId): ?OrderProduct
    {
        foreach ($this->orderProducts as $orderProduct) {
            if ($orderProductId === $orderProduct->getId()) {
                return $orderProduct;
            }
        }

        return null;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPaymentType(): ?int
    {
        return $this->paymentType;
    }

    /**
     * @param int|null $paymentType
     *
     * @return $this
     */
    public function setPaymentType(?int $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * @param string|null $note
     *
     * @return $this
     */
    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCoupon(): ?PromotionCoupon
    {
        return $this->coupon;
    }

    public function setCoupon(?PromotionCoupon $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    public function getTransactionData(): ?array
    {
        return $this->transactionData;
    }

    public function setTransactionData(?array $transactionData): self
    {
        $this->transactionData = $transactionData;

        return $this;
    }

    public function getToken(): string
    {
        if (!is_string($this->token)) {
            return $this->token->__toString();
        }

        return $this->token;
    }

    public function setToken(): self
    {
        $this->token = Uuid::uuid4()->toString();

        return $this;
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->orderProducts as $orderProduct) {
            $total += $orderProduct->getTotal();
        }

        return $total;
    }
}
