<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;
use Ixopay\Client\Transaction\Result;

final class OrderPaymentRequestView
{

    public function __construct(
        private readonly IntesaPaymentView $intesaPaymentView,
        private readonly BankArtPaymentView $bankArtPaymentView,
    ) {}

    public function view(ShopOrder $order, string $locale, null|Result $result = null): array
    {
        if ($order->getPaymentType() === ShopOrder::PAYMENT_TYPE_ON_DELIVERING) {
            return [];
        }

        if (null !== $result) {
            return $this->bankArtPaymentView->view($result);
        }

        return $this->intesaPaymentView->view($order, $locale);
    }
}
