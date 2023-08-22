<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Tags;
use App\Entity\TagTranslation;

final class TagView
{
    private array $locales;

    public function __construct(string $locales)
    {
        $this->locales = explode('|', $locales);
    }
    public function view(Tags $tags): array
    {
        $view = [
            'related_type' => $tags->getRelatedType(),
            'product_type' => $tags->getProductType(),
        ];

        foreach ($this->locales as $locale) {
            $view[$locale] = $this->getTranslationValues($tags->getByLocale($locale));
        }

        return $view;
    }

    public function getForOptions(Tags $tags, string $locale): array
    {
        $trans = $tags->getByLocale($locale);

        return [
            'title' => $trans->getTitle(),
            'value' => $tags->getId(),
        ];
    }

    private function getTranslationValues(?TagTranslation $tagTranslation): array
    {
        $data = [
            'title' => null,
            'slug' => null,
        ];

        if (null !== $tagTranslation) {
            $data = [
                'title' => $tagTranslation->getTitle(),
                'slug' => $tagTranslation->getSlug(),
            ];
        }

        return $data;
    }
}
