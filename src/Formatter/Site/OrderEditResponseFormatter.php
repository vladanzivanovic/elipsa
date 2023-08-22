<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\ShopOrder;
use App\View\OrderView;

final class OrderEditResponseFormatter
{
    private OrderView $orderView;

    public function __construct(
        OrderView $orderView
    ) {
        $this->orderView = $orderView;
    }

    public function formatResponse(ShopOrder $order, string $locale): array
    {
        return $this->orderView->view($order, $locale);
    }
}
