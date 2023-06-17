<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ProductColor;

final class ColorView
{
    public function productPageView(ProductColor $color): array
    {
        $view = [
            'id' => $color->getId(),
            'hex' => $color->getHex(),
        ];

        return $view;
    }
}
