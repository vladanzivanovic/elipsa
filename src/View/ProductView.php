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
        $view = $this->view($product, 'rs');

        $view['price'] = $product->getPrice();
        $view['discount'] = $product->getDiscount();
        $view['translations'] = $this->getTranslationValues($product);

        return $view;
    }

    public function view(Product $product, string $locale, ?User $user = null): array
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
            'is_wish' => null !== $user && $product->isUserWish($user),
        ];

        $view['translations'] = $this->getTranslationValues($product);

        if (0 < $discount) {
            $percentage = (int) round(abs((100 - ($discount/$price) * 100)));

            $view['discount'] = [
                'price' => $this->priceView->view($discount, $locale),
                'percentage' => 100 !== $percentage ? $percentage : 0,
                'saving' => $this->priceView->view($discount - $price, $locale),
            ];
        }

        $view['_links'] = $this->getLinks($view['translations']);

        return $view;
    }

    private function getTranslationValues(Product $product): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $productTranslation = $product->getByLocale($locale);

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
