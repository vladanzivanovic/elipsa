<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Entity\User;
use App\Factory\NumberFormatterFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ProductView
{
    public function __construct(
        private readonly PriceView $priceView,
        private readonly ProductSizeView $productSizeView,
        private readonly ProductOptionsView $productOptionsView,
        private readonly RouterInterface $router,
        private readonly string $defaultLocale,
        private readonly array $locales,
        private readonly array $countries,
    ) {}

    public function editView(Product $product): array
    {
        $view = $this->view($product);

//        $view['price'] = $product->getPrice();
//        $view['discount'] = $product->getDiscount();
        $view['translations'] = $this->getTranslationValues($product);
        $view['available_countries'] = array_keys($view['options']);

//        dd($view);

        return $view;
    }

    public function view(Product $product, null|User $user = null): array
    {
        //todo check where to set promo discount, productOptions or product
//        $discount = $product->getPromoDiscount($countryCode) ?? $product->getDiscount($countryCode);
//        $price = $product->getPrice();

        $view = [
            'id' => $product->getId(),
            'code' => $product->getCode(),
//            'price' => $this->priceView->view($price, $locale),
//            'show_home_page' => $product->getShowHomePage(),
//            'is_sold' => $product->isSold(),
//            'discount' => null,
            'is_wish' => $user instanceof User && $product->isUserWish($user),
//            'sizes' => $this->getSizes($product),
            'options' => [],
        ];

        $view['translations'] = $this->getTranslationValues($product);

        foreach ($this->countries as $countryCode => $country) {
            $productOptions = $product->getOptionsByCountry($countryCode);

            if (null === $productOptions) {
//                $view['options'][$countryCode] = [];

                continue;
            }

            $view['options'][$countryCode] = $this->productOptionsView->view($productOptions);
        }


//        if (0 < $discount) {
//            $percentage = (int) round(abs((100 - ($discount/$price) * 100)));
//
//            $view['discount'] = [
//                'price' => $this->priceView->view($discount, $locale),
//                'percentage' => 100 !== $percentage ? $percentage : 0,
//                'saving' => $this->priceView->view($discount - $price, $locale),
//            ];
//        }

        $view['_links'] = $this->getLinks($view['translations']);

        return $view;
    }

    private function getTranslationValues(Product $product): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $productTranslation = $product->getByLocale($locale);

            if(null === $productTranslation) {
                $productTranslation = $product->getByLocale($this->defaultLocale);
            }

            $translations[$locale] = [
                'title' => $productTranslation->getTitle(),
                'slug' => $productTranslation->getSlug(),
                'short_description' => $productTranslation->getShortDescription(),
                'description' => $productTranslation->getDescription(),
                'cleaning' => $productTranslation->getCleaning(),
            ];
        }

        return $translations;
    }

    private function getLinks(array $translations): array
    {
        $links = [];

        foreach ($translations as $locale => $translation) {
            $params = ['slug' => $translation['slug'], '_locale' => $locale];

            $links[$locale] = $this->router->generate(
                'site.product_page',
                $params,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $links;
    }
}
