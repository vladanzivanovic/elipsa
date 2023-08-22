<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Category;
use App\Entity\CategoryTranslation;

final class CategoryView
{
    public function productPageView(Category $category, string $locale): array
    {
        $view = [

        ];

        return $view + $this->getTranslation($category->getByLocale($locale));
    }

    private function getTranslation(CategoryTranslation $categoryTranslation): array
    {
        return [
            'title' => $categoryTranslation->getTitle(),
            'slug' => $categoryTranslation->getSlug(),
        ];
    }
}
