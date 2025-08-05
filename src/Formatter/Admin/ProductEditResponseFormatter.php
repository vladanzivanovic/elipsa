<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Entity\ProductOptions;
use App\Formatter\Options\TagOptionsFormatter;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ProductCleaningRepository;
use App\Repository\ProductOptionsRepository;
use App\Repository\TagsRepository;
use App\View\ImageView;
use App\View\Product\ProductView;
use App\View\YoutubeView;

final class ProductEditResponseFormatter
{
    public function __construct(
        private readonly CategoryTranslationRepository $categoryTranslationRepository,
        private readonly TagsRepository $tagsRepository,
        private readonly ProductCleaningRepository $cleaningRepository,
        private readonly ProductView $productView,
        private readonly ImageView $imageView,
        private readonly YoutubeView $youtubeView,
        private readonly TagOptionsFormatter $tagOptionsFormatter,
        private readonly ProductOptionsRepository $productOptionsRepository,
        private readonly array $countries,
    ) {}

    public function formatResponse(array $options, null|Product $product = null): array
    {
        $payload = [];

        if ($product instanceof Product) {
            $response = [
                'selectedCategories' => array_column($this->categoryTranslationRepository->getByProduct($product), 'slug'),
                'selectedTags' => $this->getSelectedTags($product),
                'cleaning_box' => array_column($this->cleaningRepository->getByProduct($product), 'icon'),
            ];

            $payload = $response +
                $this->productView->editView($product) +
                $this->getImages($product) +
                $this->getYoutube($product);
        }

        $formattedOptions = [
            'tags' => $this->tagOptionsFormatter->formatTagOptions(),
            'categories' => $options['categories'],
            'sizes' => $options['sizes'],
            'colors' => $options['colors'],
            'homePagePosition' => $this->getHighestHomePagePosition(),
        ];



        return [
            'payload' => $payload,
            'options' => $formattedOptions,
        ];
    }

    private function getImages(Product $product): array
    {
        $images = [];

        foreach ($product->getProductHasImages() as $productHasImage) {
            $images[] = $this->imageView->editProductView($productHasImage, 'product');
        }

        return ['selectedImages' => $images];
    }

    private function getYoutube(Product $product): array
    {
        $youtubes = [];

        foreach ($product->getYoutubes() as $youtube) {
            $youtubes[] = $this->youtubeView->view($youtube);
        }

        return ['youtubes' => $youtubes];
    }

    private function getHighestHomePagePosition(): array
    {
        $homePagePositions = [];

        foreach ($this->countries as $countryCode => $country) {
            $optionUp = $this->productOptionsRepository->getHighestHomePagePosition(ProductOptions::HOME_PAGE_UP, $countryCode);
            $optionDown = $this->productOptionsRepository->getHighestHomePagePosition(ProductOptions::HOME_PAGE_DOWN, $countryCode);

            $homePagePositions[$countryCode] = [
                ProductOptions::HOME_PAGE_UP => $optionUp['showHomePage'][ProductOptions::HOME_PAGE_UP] ?? null,
                ProductOptions::HOME_PAGE_DOWN => $optionDown['showHomePage'][ProductOptions::HOME_PAGE_DOWN] ?? null,
            ];
        }

        return $homePagePositions;
    }

    private function getSelectedTags(Product $product): array
    {
        $selectedTags = [];

        foreach ($product->getAvailableCountries() as $country) {
            $selectedTags[$country] = array_column($this->tagsRepository->getByProductAdmin($product, $country), 'id');
        }

        return $selectedTags;
    }
}
