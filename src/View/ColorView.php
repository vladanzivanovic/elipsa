<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ColorTranslation;
use App\Entity\ProductColor;

final class ColorView
{
    private array $locales;

    public function __construct(
        string $locales
    ) {
        $this->locales = explode('|', $locales);
    }
    public function productPageView(ProductColor $color): array
    {
        $translations = [];

        $view = [
            'id' => $color->getId(),
            'hex' => $color->getHex(),
        ];

        foreach ($this->locales as $locale) {
            $translations[$locale] = $this->getTranslationValues($color->getByLocale($locale));
        }

        $view['translations'] = $translations;

        return $view;
    }

    private function getTranslationValues(ColorTranslation $colorTranslation): array
    {
        return [
            'title' => $colorTranslation->getTitle(),
            'slug' => $colorTranslation->getSlug(),
        ];
    }
}
