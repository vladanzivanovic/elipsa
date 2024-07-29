<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Settings;

final class SettingsEditPageFormatter
{
    public function format(array $data): array
    {
        $formattedPrices = [];

        /** @var Settings $priceSettings */
        foreach ($data['settings']['shipping_prices'] as $priceSettings) {
            $price = number_format($priceSettings->getValue()/100, 2, '.', '');
            $priceSettings->setValue((string) $price);

            $formattedPrices[$priceSettings->getCountry()][] = $priceSettings;
        }

        $data['settings']['shipping_prices'] = $formattedPrices;

        return $data;
    }
}
