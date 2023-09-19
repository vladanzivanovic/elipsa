<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Tags;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;

final class ShopFilterCollector
{
    private ProductSizeRepository $sizeRepository;

    private ProductRepository $productRepository;

    private ProductColorRepository $colorRepository;

    private TagsRepository $tagsRepository;

    public function __construct(
        ProductSizeRepository $sizeRepository,
        ProductRepository $productRepository,
        ProductColorRepository $colorRepository,
        TagsRepository $tagsRepository
    ) {
        $this->sizeRepository = $sizeRepository;
        $this->productRepository = $productRepository;
        $this->colorRepository = $colorRepository;
        $this->tagsRepository = $tagsRepository;
    }
    public function collect(string $locale): array
    {
        $sizes = $this->sizeRepository->getForOptions();
        $prices = $this->productRepository->getLowestAndHighestPrice();

        $data = [
            'sizes'     => $sizes,
            'colors' => $this->colorRepository->getByLocale($locale),
            'collection' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_COLLECTION),
            'season' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_SEASON),
            'attributes' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_ATTRIBUTE),
            'price'    => $prices[0],
        ];

        return $data;
    }
}
