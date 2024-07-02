<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Tags;
use App\Entity\TagTranslation;

final class TagView
{
    public function __construct(
        private readonly string $defaultLocale,
        private readonly array $locales,
    ) {}

    public function view(Tags $tags): array
    {
        $view = [
            'related_type' => $tags->getRelatedType(),
            'product_type' => $tags->getProductType(),
            'translations' => $this->getTranslationValues($tags),
        ];

        return $view;
    }

    public function getForOptions(Tags $tags, string $locale ): array
    {
        $trans = $tags->getByLocale($locale);

        return [
            'title' => $trans->getTitle(),
            'value' => $tags->getId(),
            'slug' => $trans->getSlug(),
        ];
    }

    private function getTranslationValues(Tags $tag): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $trans = $tag->getByLocale($locale);

            if (null === $trans) {
                $trans = $tag->getByLocale($this->defaultLocale);
            }

            $translations[$locale] = [
                'title' => $trans->getTitle(),
                'slug' => $trans->getSlug(),
            ];
        }

        return $translations;
    }
}
