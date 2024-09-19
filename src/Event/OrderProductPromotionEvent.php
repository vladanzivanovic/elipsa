<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\ShopOrder;
use Symfony\Contracts\EventDispatcher\Event;

final class OrderProductPromotionEvent extends Event
{
    public const ORDER_PRODUCT_PROMOTION = 'order.product.promotion';

    private bool $orderProductChanged = false;

    public function __construct(
        private readonly ShopOrder $order
    ) {}

    public function getOrder(): ShopOrder
    {
        return $this->order;
    }

    public function setIsOrderProductChanged(bool $isOrderProductChanged): void
    {
        $this->orderProductChanged = $isOrderProductChanged;
    }

    public function isOrderProductChanged(): bool
    {
        return $this->orderProductChanged;
    }
}
