<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Collector\Admin\ProductPageOptionsCollector;

class ProductsPageResponseFormatter
{
    private ProductPageOptionsCollector $productPageOptionsCollector;

    public function __construct(
        ProductPageOptionsCollector $productPageOptionsCollector
    ) {
        $this->productPageOptionsCollector = $productPageOptionsCollector;
    }

    public function format()
    {
        $response = [
            'options' => $this->productPageOptionsCollector->options(),
        ];

        return $response;
    }
}
