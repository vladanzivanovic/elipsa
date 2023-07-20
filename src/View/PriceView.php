<?php

declare(strict_types=1);

namespace App\View;

use App\Factory\NumberFormatterFactory;

final class PriceView
{
    private NumberFormatterFactory $numberFormatterFactory;

    private array $languages;

    public function __construct(
        NumberFormatterFactory $numberFormatterFactory,
        array $languages
    ) {

        $this->numberFormatterFactory = $numberFormatterFactory;
        $this->languages = $languages;
    }
    public function view(int $price, string $locale): array
    {
        $language = $this->languages[$locale];

        $numberFormatter = $this->numberFormatterFactory->create($language['locale'], \NumberFormatter::DECIMAL);
        $numberFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);

        return [
            'amount' => $numberFormatter->format($price),
            'currency' => $language['currencyCode'],
        ];
    }
}
