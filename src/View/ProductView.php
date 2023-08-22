<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Entity\User;
use App\Factory\NumberFormatterFactory;
use Symfony\Component\Routing\RouterInterface;

final class ProductView
{
    private PriceView $priceView;

    private RouterInterface $router;

    private array $locales;

    public function __construct(
        PriceView $priceView,
        RouterInterface $router,
        string $locales
    ) {
        $this->locales = explode('|', $locales);
        $this->priceView = $priceView;
        $this->router = $router;
    }

    public function editView(Product $product): array
    {
        $translations = [];
        $view = $this->view($product, 'rs');

        foreach ($this->locales as $locale) {
            $translations[$locale] = $this->getTranslationValues($product->getByLocale($locale));
        }

        $view['price'] = $product->getPrice();
        $view['discount'] = $product->getDiscount();
        $view['translations'] = $translations;

        return $view;
    }

    public function view(Product $product, string $locale, ?User $user = null): array
    {
        $discount = $product->getDiscount();
        $price = $product->getPrice();
        $trans = $product->getByLocale($locale);

        $view = [
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'price' => $this->priceView->view($price, $locale),
            'show_home_page' => $product->getShowHomePage(),
            'is_sold' => $product->isSold(),
            'discount' => null,
            'is_wish' => null !== $user && $product->isUserWish($user),
        ];

        $view['translations'][$locale] = $this->getTranslationValues($trans);

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
