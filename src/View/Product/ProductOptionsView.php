<?php

declare(strict_types=1);

namespace App\View\Product;

use App\Entity\ProductOptions;
use App\View\PriceView;

class ProductOptionsView
{
    public function __construct(
        private readonly PriceView $priceView,
        private readonly ProductSizeView $productSizeView,
        private readonly string $defaultLocale,
    ){}

    public function view(ProductOptions $productOptions, null|string $locale = null): array
    {
        $locale = $locale ?? $this->defaultLocale;
        $discount = $productOptions->getPromoDiscount() ?? $productOptions->getDiscount();
        $price = $productOptions->getPrice();

        $view = [
            'id' => $productOptions->getId(),
            'price' => $this->priceView->view($price, $locale),
            'show_home_page' => $productOptions->getShowHomePage() ?? [],
            'is_sold' => $productOptions->isSold(),
            'discount' => null,
            'sizes' => $this->getSizes($productOptions),
        ];

        if (0 < $discount) {
            $percentage = (int) round(abs((100 - ($discount/$price) * 100)));

            $view['discount'] = [
                'price' => $this->priceView->view($discount, $locale),
                'percentage' => 100 !== $percentage ? $percentage : 0,
                'saving' => $this->priceView->view($discount - $price, $locale),
            ];
        }

        return $view;
    }

    private function getSizes(ProductOptions $productOptions): array
    {
        $sizes = [];

        foreach ($productOptions->getProductHasSizes() as $productHasSize) {
            $sizes[] = $this->productSizeView->view($productHasSize);
        }

        return $sizes;
    }
}
