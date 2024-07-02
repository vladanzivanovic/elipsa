<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\OfficeContact;
use App\Entity\ProductColor;
use App\View\ColorView;

final class ColorEditResponseFormatter
{
    public function __construct(
        private readonly ColorView $colorView
    ) {}

    public function formatResponse(ProductColor $productColor = null): array
    {
        $response = [];

        if ($productColor instanceof ProductColor) {
            $response['payload'] = $this->colorView->view($productColor);
        }

        return $response;
    }
}
