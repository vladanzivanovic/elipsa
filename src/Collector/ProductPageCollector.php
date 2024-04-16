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
    public function __construct(
        private readonly ProductColorRepository $colorRepository,
        private readonly ProductSizeRepository $sizeRepository,
        private readonly TagsRepository $tagsRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly ImageRepository $imageRepository,
        private readonly ProductRepository $productRepository,
        private readonly ProductCleaningRepository $cleaningRepository,
        private readonly string $defaultLocale,
    ) {}

    /**
     * @throws \Exception
     */
    public function collect(ProductTranslation $productTranslation, string $locale, User|null $user): array
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

            $trans = $category->getByLocale($locale);

            if (null === $trans) {
                $trans = $category->getByLocale($this->defaultLocale);
            }

            $categories[] = $trans->getSlug();
        }

        return $this->productRepository->getRelatedProducts($categories, $product, $user);
    }
}
