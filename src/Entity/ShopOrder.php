<?php

namespace App\Entity;

use App\Entity\Resources\EntityInterface;
use App\Entity\Resources\PromotionEligibilityInterface;
use App\Entity\Resources\ResourceTrait;
use App\EventListener\OrderShippingPriceEventListener;
use App\Repository\ShopOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: ShopOrderRepository::class)]
#[ORM\EntityListeners([OrderShippingPriceEventListener::class])]
class ShopOrder implements EntityInterface, PromotionEligibilityInterface
{
    use ResourceTrait;
    use TimestampableEntity;

    public const STATUS_NEW = 'new'; //1;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PREPARING = 'preparing';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELED = 'canceled';

    public const STATUS_FAILED = 'failed';

    public const CARD_STATUS_PRE_AUTH = 'PreAuth'; //4
    public const CARD_STATUS_POST_AUTH = 'PostAuth';
    public const CARD_STATUS_REFUND = 'Credit';
    public const CARD_STATUS_VOID = 'Void';
//    public const CARD_STATUS_REFUND = 'refund'; //5;
//    public const CARD_STATUS_VOID = 'void'; //6;
    public const CARD_STATUS_FAILED = 'Failed'; //3;

    public const CARD_STATUS_DECLINED = 'Declined';

    public const CARD_STATUS_REJECTED = 'Rejected';

    public const PAYMENT_TYPE_ON_DELIVERING = 'on_delivery'; //1;
    public const PAYMENT_TYPE_CREDIT_CARD = 'credit_card'; //2;

    public const SHIPPING_TYPE_ON_DELIVERING = 'on_delivery'; //1;
    public const SHIPPING_TYPE_IN_STORE = 'in_store'; //2;

    public const SHIPPING_STATUS_PENDING = self::STATUS_PENDING;

    public const SHIPPING_STATUS_PREPARING = self::STATUS_PREPARING;

    public const SHIPPING_STATUS_SHIPPED = self::STATUS_SHIPPED;

    public const SHIPPING_STATUS_COMPLETED = self::STATUS_COMPLETED;

    #[ORM\Column(type: 'string', length: 255)]
    private string $status;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    #[ORM\OneToOne(targetEntity: \App\Entity\Address::class, cascade: ['persist', 'remove'])]
    private ?Address $billingAddress = null;

    #[ORM\OneToOne(targetEntity: \App\Entity\Address::class, cascade: ['persist', 'remove'])]
    private ?Address $shippingAddress = null;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\OrderProduct::class, mappedBy: 'orderId', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $orderProducts;

    #[ORM\ManyToOne(targetEntity: \App\Entity\User::class, inversedBy: 'shopOrders', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $paymentType = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\ManyToOne(targetEntity: \Promotion::class, inversedBy: 'shopOrders')]
    private ?Promotion $coupon = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $transactionData = [];

    #[ORM\Column(type: 'uuid', unique: true)]
    private string $token;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $trackingInfo = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cardStatus = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $shippingType = null;

    #[ORM\Column(type: 'boolean')]
    private bool $visited = false;

    #[ORM\ManyToOne(targetEntity: Location::class)]
    private ?Location $storeId = null;

    #[ORM\Column(length: 5)]
    private ?string $country = null;

    #[ORM\Column(nullable: true)]
    private null|int $shippingPrice = null;

    public function __construct()
    {
        $this->orderProducts = new ArrayCollection();
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
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

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function setPaymentType(string $paymentType): void
    {
        $this->paymentType = $paymentType;
    }

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

    public function getCoupon(): ?Promotion
    {
        return $this->coupon;
    }

    public function setCoupon(?Promotion $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    public function getTransactionData(): ?array
    {
        return $this->transactionData;
    }

    public function setTransactionData(null|array $transactionData): self
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

    public function getTrackingInfo(): ?array
    {
        return $this->trackingInfo;
    }

    public function setTrackingInfo(?array $trackingInfo): self
    {
        $this->trackingInfo = $trackingInfo;

        return $this;
    }

    public function getCardStatus(): ?string
    {
        return $this->cardStatus;
    }

    public function setCardStatus(?string $cardStatus): self
    {
        $this->cardStatus = $cardStatus;

        return $this;
    }

    public function getShippingType(): ?string
    {
        return $this->shippingType;
    }

    public function setShippingType(string $shippingType): self
    {
        $this->shippingType = $shippingType;

        return $this;
    }

    public function isVisited(): ?bool
    {
        return $this->visited;
    }

    public function setVisited(bool $visited): self
    {
        $this->visited = $visited;

        return $this;
    }

    public function getStoreId(): ?Location
    {
        return $this->storeId;
    }

    public function setStoreId(?Location $storeId): self
    {
        $this->storeId = $storeId;

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

    public function getShippingPrice(): null|int
    {
        return $this->shippingPrice;
    }

    public function setShippingPrice(int $shippingPrice): static
    {
        $this->shippingPrice = $shippingPrice;

        return $this;
    }
}
