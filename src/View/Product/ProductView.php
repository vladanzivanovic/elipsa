<?php

declare(strict_types=1);

namespace App\View\Product;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ProductView
{
    public function __construct(
        private readonly ProductOptionsView $productOptionsView,
        private readonly RouterInterface $router,
        private readonly array $locales,
        private readonly array $countries,
    ) {}

    public function editView(Product $product): array
    {
        $view = $this->view($product);

        $view['translations'] = $this->getTranslationValues($product);
        $view['available_countries'] = array_keys($view['options']);

        return $view;
    }

    public function view(Product $product, null|User $user = null): array
    {
        $view = [
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'is_wish' => $user instanceof User && $product->isUserWish($user),
            'has_free_shipping' => $product->isFreeShippingEnabled(),
            'options' => [],
        ];

        $view['translations'] = $this->getTranslationValues($product);

        foreach ($this->countries as $countryCode => $country) {
            $this->getOptions($product, $countryCode, $view['options']);
        }

        $view['_links'] = $this->getLinks($view['translations']);

        return $view;
    }

    private function getTranslationValues(Product $product): array
    {
        $translations = [];

        foreach ($this->locales as $locale) {
            $productTranslation = $product->getByLocale($locale);

            if(null === $productTranslation) {
                continue;
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

    private function getOptions(Product $product, string $countryCode, array &$options): void
    {
        $productOptions = $product->getOptionByCountry($countryCode);

        if (null !== $productOptions) {
            $options[$countryCode] = $this->productOptionsView->view($productOptions, $countryCode);
        }
    }
}
