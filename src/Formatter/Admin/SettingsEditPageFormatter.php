<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Settings;

final class SettingsEditPageFormatter
{
    public function format(array $data): array
    {
        $formattedPrices = [];

        /** @var Settings $price */
        foreach ($data['settings']['shipping_prices'] as $price) {
            $formattedPrices[$price->getCountry()][] = $price;
        }

        $data['settings']['shipping_prices'] = $formattedPrices;

        return $data;
    }
}
