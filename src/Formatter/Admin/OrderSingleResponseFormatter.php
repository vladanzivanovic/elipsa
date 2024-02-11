<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\ShopOrder;
use App\View\OrderView;

final class OrderSingleResponseFormatter
{
    private OrderView $orderView;

    private string $adminLocale;

    public function __construct(
        OrderView $orderView,
        string $adminLocale
    ) {
        $this->orderView = $orderView;
        $this->adminLocale = $adminLocale;
    }

    /**
     * @param ShopOrder $order
     *
     * @return array
     */
    public function formatResponse(ShopOrder $order): array
    {
        $payload = $this->orderView->view($order, $this->adminLocale);

//        dd($payload);

        return ['payload' => $payload];
    }
}
