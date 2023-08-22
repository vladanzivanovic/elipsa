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
        array $settings,
        string $locale,
        bool $isSuccessfulTransaction
    ): array {
        $view = $this->orderView->view($order, $locale);
        $view['locale'] = $locale;

        $view['seller'] = [
            'name'      => $settings['site_name']->getValue(),
            'pib'       => $settings['pib']->getValue(),
            'account'   => $settings['account_number']->getValue(),
            'email'     => $settings['main_email']->getValue(),
            'telephone' => $settings['telephone']->getValue(),
            'mobile'    => $settings['mobile_phone']->getValue(),
            'address'   => $settings['street']->getValue().', '.$settings['zip_code']->getValue().' '.$settings['city']->getValue(),
        ];

        $view['is_successful_transaction'] = $isSuccessfulTransaction;

        return $view;
    }

    private function getUserRegistrationData(User $user, string $locale): array
    {

        if (null === $user->getResetToken()) {
            return [];
        }

        return [
            'registration_token' => $user->getResetToken(),
            'locale' => $locale
        ];
    }
}
