<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Category;
use App\Formatter\Options\CategoryOptionsFormatter;
use App\View\CategoryView;

final class CategoryEditFormatter
{
    public function __construct(
        private readonly CategoryView $categoryView,
        private readonly CategoryOptionsFormatter $categoryOptionsFormatter,
        private readonly string $defaultLocale,
    ) {}

    public function format(Category $category = null): array
    {
        $formattedData = [
            'options' => $this->categoryOptionsFormatter->format($this->defaultLocale),
        ];

        if (null !== $category) {
            $formattedData['payload'] = $this->categoryView->view($category);
        }

        return $formattedData;
    }
}
