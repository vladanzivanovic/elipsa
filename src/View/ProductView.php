<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Factory\NumberFormatterFactory;

final class ProductView
{
    private array $locales;

    private PriceView $priceView;

    public function __construct(
        PriceView $priceView,
        string $locales
    ) {
        $this->locales = explode('|', $locales);
        $this->priceView = $priceView;
    }

    public function editView(Product $product): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $translations[$locale] = $this->getTranslationValues($product->getByLocale($locale));
        }

        return $translations + $this->view($product, 'rs');
    }

    public function singlePageView(Product $product, string $locale): array
    {
        $translationView = $this->getTranslationValues($product->getByLocale($locale));

        return $translationView + $this->view($product, $locale);
    }

    public function gridView(Product $product, string $locale): array
    {
        $translationView = $this->getTranslationValues($product->getByLocale($locale));

        return $translationView + $this->view($product, $locale);
    }

    public function view(Product $product, string $locale): array
    {
        $discount = $product->getDiscount();
        $price = $product->getPrice();

        $view = [
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'price' => $this->priceView->view($price, $locale),
            'show_home_page' => $product->getShowHomePage(),
            'is_sold' => $product->isSold(),
            'discount' => null,
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

    private function getTranslationValues(ProductTranslation $productTranslation): array
    {
        return [
            'title' => $productTranslation->getTitle(),
            'slug' => $productTranslation->getSlug(),
            'short_description' => $productTranslation->getShortDescription(),
            'description' => $productTranslation->getDescription(),
            'cleaning' => $productTranslation->getCleaning(),
        ];
    }
}
