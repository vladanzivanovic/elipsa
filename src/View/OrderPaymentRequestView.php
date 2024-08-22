<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;

final class OrderPaymentRequestView
{

    public function __construct(
        private readonly IntesaPaymentView $intesaPaymentView,
        private readonly BankArtPaymentView $bankArtPaymentView,
    ) {}

    public function view(ShopOrder $order, string $locale): array
    {
        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_ON_DELIVERING) {
            return [];
        }

        if ($order->getCountry() === 'ba') {
            return $this->bankArtPaymentView->view($order, $locale);
        }

        return $this->intesaPaymentView->view($order, $locale);
    }
}
