<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Formatter\Options\TagOptionsFormatter;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ProductCleaningRepository;
use App\Repository\TagsRepository;
use App\View\ImageView;
use App\View\ProductView;
use App\View\SizeView;
use App\View\YoutubeView;

final class ProductEditResponseFormatter
{
    private CategoryTranslationRepository $categoryTranslationRepository;

    private TagsRepository $tagsRepository;

    private ProductCleaningRepository $cleaningRepository;

    private ProductView $productView;

    private ImageView $imageView;

    private YoutubeView $youtubeView;

    private TagOptionsFormatter $tagOptionsFormatter;

    public function __construct(
        CategoryTranslationRepository $categoryTranslationRepository,
        TagsRepository $tagsRepository,
        ProductCleaningRepository $cleaningRepository,
        ProductView $productView,
        ImageView $imageView,
        YoutubeView $youtubeView,
        TagOptionsFormatter $tagOptionsFormatter
    ) {
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->tagsRepository = $tagsRepository;
        $this->cleaningRepository = $cleaningRepository;
        $this->productView = $productView;
        $this->imageView = $imageView;
        $this->youtubeView = $youtubeView;
        $this->tagOptionsFormatter = $tagOptionsFormatter;
    }

    public function formatResponse(array $options, null|Product $product = null): array
    {
        $payload = [];

        if ($product instanceof Product) {
            $response = [
                'selectedCategories' => array_column($this->categoryTranslationRepository->getByProduct($product), 'slug'),
                'selectedTags' => array_column($this->tagsRepository->getByProduct($product), 'id'),
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


}
