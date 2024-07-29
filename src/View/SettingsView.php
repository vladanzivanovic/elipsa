<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Settings;
use Symfony\Component\HttpFoundation\RequestStack;

final class SettingsView
{
    public function __construct(
        private readonly PriceView $priceView,
        private readonly RequestStack $requestStack,
    ) {}

    public function view(Settings $settings): array
    {
        $view = [
            'name' => $settings->getName(),
            'slug' => $settings->getSlug(),
            'value' => $settings->getValue(),
            'locale' => $settings->getLocale(),
            'input_type' => $settings->getInputType(),
        ];

        if (in_array($settings->getSlug(), ['FREE_SHIPPING', 'FREE_SHIPPING_STORE', 'SHIPPING_PRICE'], true)) {
            $country = $this->requestStack->getCurrentRequest()->attributes->get('_country');

            $view['value'] = $this->priceView->view((int) $settings->getValue(), $country);
        }

        return  $view;
    }
}
