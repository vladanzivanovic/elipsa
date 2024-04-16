<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ColorTranslation;
use App\Entity\ProductColor;

final class ColorView
{
    private array $locales;

    public function __construct(
        private readonly string $defaultLocale,
        string $locales,
    ) {
        $this->locales = explode('|', $locales);
    }

    public function productPageView(ProductColor $color): array
    {
        $view = [
            'id' => $color->getId(),
            'hex' => $color->getHex(),
            'translations' => $this->getTranslationValues($color),
        ];

        return $view;
    }

    private function getTranslationValues(ProductColor $color): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $trans = $color->getByLocale($locale);

            if(null === $trans) {
                $trans = $color->getByLocale($this->defaultLocale);
            }

            $translations[$locale] = [
                'title' => $trans->getTitle(),
                'slug' => $trans->getSlug(),
            ];
        }

        return $translations;
    }
}
