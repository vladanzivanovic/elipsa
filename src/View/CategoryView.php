<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Category;
use App\Entity\CategoryTranslation;

final class CategoryView
{
    public function __construct(
        private readonly string $defaultLocale
    ){}

    public function productPageView(Category $category, string $locale): array
    {
        $view = [];

        return $view + $this->getTranslation($category, $locale);
    }

    private function getTranslation(Category $category, string $locale): array
    {
        $trans = $category->getByLocale($locale);

        if (null === $trans) {
            $trans = $category->getByLocale($this->defaultLocale);
        }

        return [
            'title' => $trans->getTitle(),
            'slug' => $trans->getSlug(),
        ];
    }
}
