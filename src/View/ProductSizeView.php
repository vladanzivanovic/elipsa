<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ProductHasSizes;

class ProductSizeView
{
    private SizeView $sizeView;

    public function __construct(
        SizeView $sizeView
    ) {
        $this->sizeView = $sizeView;
    }

    public function view(ProductHasSizes $productHasSizes): array
    {
        $sizeView = $this->sizeView->productPageView($productHasSizes->getSize());

        $sizeView['quantity'] = $productHasSizes->getQuantity();

        return $sizeView;
    }
}
