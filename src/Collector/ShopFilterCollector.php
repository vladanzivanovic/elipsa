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
    public function __construct(
        private readonly ProductSizeRepository $sizeRepository,
        private readonly ProductRepository $productRepository,
        private readonly ProductColorRepository $colorRepository,
        private readonly TagsRepository $tagsRepository
    ) {}

    public function collect(string $locale): array
    {
        $sizes = $this->sizeRepository->getForOptions();
        $prices = $this->productRepository->getLowestAndHighestPrice();

        return [
            'sizes' => $sizes,
            'colors' => $this->colorRepository->getByLocale($locale),
            'collection' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_COLLECTION),
            'season' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_SEASON),
            'attributes' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_ATTRIBUTE),
            'price' => $prices[0],
        ];
    }
}
