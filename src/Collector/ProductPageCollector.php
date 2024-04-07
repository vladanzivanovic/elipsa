<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Product;
use App\Entity\ProductHasCategories;
use App\Entity\ProductTranslation;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductCleaningRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;

final class ProductPageCollector
{
    private \App\Repository\ProductColorRepository $colorRepository;
    private \App\Repository\ProductSizeRepository $sizeRepository;
    private \App\Repository\TagsRepository $tagsRepository;
    private \App\Repository\CategoryRepository $categoryRepository;
    private \App\Repository\ImageRepository $imageRepository;
    private \App\Repository\ProductRepository $productRepository;
    private \App\Repository\ProductCleaningRepository $cleaningRepository;

    public function __construct(
        ProductColorRepository $colorRepository,
        ProductSizeRepository $sizeRepository,
        TagsRepository $tagsRepository,
        CategoryRepository $categoryRepository,
        ImageRepository $imageRepository,
        ProductRepository $productRepository,
        ProductCleaningRepository $cleaningRepository
    ) {
        $this->colorRepository = $colorRepository;
        $this->sizeRepository = $sizeRepository;
        $this->tagsRepository = $tagsRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageRepository = $imageRepository;
        $this->productRepository = $productRepository;
        $this->cleaningRepository = $cleaningRepository;
    }

    /**
     * @throws \Exception
     */
    public function collect(ProductTranslation $productTranslation, string $locale, ?User $user): array
    {
        $product = $productTranslation->getProduct();

        return [
            'translation'       => $productTranslation,
            'product'           => $product,
            'colors'            => $this->colorRepository->getByProducts([$product->getId()], $locale),
            'sizes'             => $this->sizeRepository->getByProducts([$product->getId()]),
            'tags'              => $this->tagsRepository->getByProducts([$product->getId()], $locale),
            'productCategories' => $this->categoryRepository->getByProduct($product, $locale),
            'images'            => $this->imageRepository->getByProduct($product),
            'related_products'  => $this->relatedProducts($product, $locale, $user),
            'cleaningIcons'     => array_column($this->cleaningRepository->getByProduct($product), 'icon'),
        ];
    }

    /**
     * @throws \Exception
     */
    private function relatedProducts(Product $product, string $locale, ?User $user): array
    {
        $hasCategories = $product->getProductHasCategories();

        $categories = [];

        /** @var ProductHasCategories $hasCategory */
        foreach ($hasCategories->getIterator() as $hasCategory) {
            $category = $hasCategory->getCategory();
            $categories[] = $category->getTranslationByLocale($locale)->first()->getSlug();
        }

        return $this->productRepository->getRelatedProducts($categories, $product, $user);
    }
}
