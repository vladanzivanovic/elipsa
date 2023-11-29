<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\View\ProductView;

class ProductSearchResponseFormatter
{
    private ProductView $productView;

    private string $defaultLocale;

    public function __construct(
        ProductView $productView,
        string $defaultLocale
    ) {
        $this->productView = $productView;
        $this->defaultLocale = $defaultLocale;
    }

    public function format(array $products): array
    {
        $formattedArray = [];

        foreach ($products as $product) {
            $formattedArray[] = $this->productView->view($product, $this->defaultLocale);
        }

        return ['payload' => $formattedArray];
    }
}
