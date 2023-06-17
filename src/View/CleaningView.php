<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ProductCleaning;

final class CleaningView
{
    public function productPageView(ProductCleaning $productCleaning): array
    {
        $view = [
            'icon' => $productCleaning->getIcon(),
        ];

        return $view;
    }
}
