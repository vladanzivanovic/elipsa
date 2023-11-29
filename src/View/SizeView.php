<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ProductSize;

final class SizeView
{
    public function productPageView(ProductSize $size): array
    {
        return [
            'size' => $size->getSize(),
            'slug' => $size->getSlug(),
        ];
    }
}
