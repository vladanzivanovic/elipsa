<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Product;
use App\Repository\CategoryTranslationRepository;
use App\Repository\ProductCleaningRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use App\View\ImageView;
use App\View\ProductView;
use App\View\YoutubeView;

final class ProductEditResponseFormatter
{
    private CategoryTranslationRepository $categoryTranslationRepository;

    private TagsRepository $tagsRepository;

    private ProductSizeRepository $sizeRepository;

    private ProductCleaningRepository $cleaningRepository;

    private ProductView $productView;

    private ImageView $imageView;

    private YoutubeView $youtubeView;

    public function __construct(
        CategoryTranslationRepository $categoryTranslationRepository,
        TagsRepository $tagsRepository,
        ProductSizeRepository $sizeRepository,
        ProductCleaningRepository $cleaningRepository,
        ProductView $productView,
        ImageView $imageView,
        YoutubeView $youtubeView
    ) {
        $this->categoryTranslationRepository = $categoryTranslationRepository;
        $this->tagsRepository = $tagsRepository;
        $this->sizeRepository = $sizeRepository;
        $this->cleaningRepository = $cleaningRepository;
        $this->productView = $productView;
        $this->imageView = $imageView;
        $this->youtubeView = $youtubeView;
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function formatResponse(Product $product): array
    {
        $response = [
            'selectedCategories' => array_column($this->categoryTranslationRepository->getByProduct($product), 'slug'),
            'selectedTags' => array_column($this->tagsRepository->getByProduct($product), 'mainSlug'),
            'selectedSizes' => array_column($this->sizeRepository->getByProduct($product), 'slug'),
            'cleaning_box' => array_column($this->cleaningRepository->getByProduct($product), 'icon'),
        ];

        return ['payload' => $response +
            $this->productView->editView($product) +
            $this->getImages($product) +
            $this->getYoutube($product)];
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
