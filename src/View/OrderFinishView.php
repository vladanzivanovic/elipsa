<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;
use App\Entity\User;

final class OrderFinishView
{
    private OrderView $orderView;

    public function __construct(
        OrderView $orderView
    ) {
        $this->orderView = $orderView;
    }

    public function view(
        ShopOrder $order,
        array $officeInfo,
        string $locale,
        bool $isSuccessfulTransaction
    ): array {
        $view = $this->orderView->view($order, $locale);

        $view['seller'] = [
            'name'      => $officeInfo['settings']['site_name']->getValue(),
            'pib'       => $officeInfo['settings']['pib']->getValue(),
            'account'   => $officeInfo['settings']['account_number']->getValue(),
            'email'     => $officeInfo['settings']['main_email']->getValue(),
            'address'   => $officeInfo['settings']['street']->getValue().', '.$officeInfo['settings']['zip_code']->getValue().' '.$officeInfo['settings']['city']->getValue(),
            'contacts' => $officeInfo['office_contacts']
        ];

        $view['is_successful_transaction'] = $isSuccessfulTransaction;

        return $view;
    }
}
