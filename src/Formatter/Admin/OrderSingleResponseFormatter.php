<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\ShopOrder;
use App\View\OrderView;

final class OrderSingleResponseFormatter
{
    public function __construct(
        private readonly OrderView $orderView,
        private readonly string $adminLocale,
        private readonly array $countries
    ) {}

    public function formatResponse(ShopOrder $order): array
    {
        $locale = $this->adminLocale;

        foreach ($this->countries as $countryCode => $country) {
            if ($order->getCountry() !== $countryCode) {
                continue;
            }

            $locale = $order->getCountry();
        }

        $payload = $this->orderView->view($order, $locale, $order->getCountry());

        return ['payload' => $payload];
    }
}
