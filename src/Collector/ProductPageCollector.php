<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Product;
use App\Entity\ProductHasCategories;
use App\Entity\ProductTranslation;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;

final class ProductPageCollector
{
    /**
     * @var ProductColorRepository
     */
    private $colorRepository;
    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;
    /**
     * @var TagsRepository
     */
    private $tagsRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductColorRepository $colorRepository
     * @param ProductSizeRepository  $sizeRepository
     * @param TagsRepository         $tagsRepository
     * @param CategoryRepository     $categoryRepository
     * @param ImageRepository        $imageRepository
     * @param ProductRepository      $productRepository
     */
    public function __construct(
        ProductColorRepository $colorRepository,
        ProductSizeRepository $sizeRepository,
        TagsRepository $tagsRepository,
        CategoryRepository $categoryRepository,
        ImageRepository $imageRepository,
        ProductRepository $productRepository
    ) {
        $this->colorRepository = $colorRepository;
        $this->sizeRepository = $sizeRepository;
        $this->tagsRepository = $tagsRepository;
        $this->categoryRepository = $categoryRepository;
        $this->imageRepository = $imageRepository;
        $this->productRepository = $productRepository;
    }

    public function collect(ProductTranslation $productTranslation, string $locale): array
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
            'related_products'  => $this->relatedProducts($product, $locale),
        ];
    }

    private function relatedProducts(Product $product, string $locale): array
    {
        $hasCategories = $product->getProductHasCategories();

        $categories = [];

        /** @var ProductHasCategories $hasCategory */
        foreach ($hasCategories->getIterator() as $hasCategory) {
            $category = $hasCategory->getCategory();
            $categories[] = $category->getTranslationByLocale($locale)->first()->getSlug();
        }

        $products = $this->productRepository->getRelatedProducts($locale, $categories, $product);

        $productIds = array_column($products, 'id');

        $productColors = $this->colorRepository->getByProducts($productIds, $locale);
        $productSizes = $this->sizeRepository->getByProducts($productIds);
        $productTags = $this->tagsRepository->getByProducts($productIds, $locale);

        return [
            'products'  => $products,
            'product_colors'    => $productColors,
            'product_sizes'     => $productSizes,
            'product_tags'      => $productTags,

        ];
    }
}