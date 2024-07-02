<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\View\ProductView;

class ProductSearchResponseFormatter
{
    public function __construct(
        private readonly ProductView $productView,
    ) {}

    public function format(array $products): array
    {
        $formattedArray = [];

        foreach ($products as $product) {
            $formattedArray[] = $this->productView->view($product);
        }

        return ['payload' => $formattedArray];
    }
}
