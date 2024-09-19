<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Tags;
use App\Repository\ProductColorRepository;
use App\Repository\ProductOptionsRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;

final class ShopFilterCollector
{
    public function __construct(
        private readonly ProductSizeRepository $sizeRepository,
        private readonly ProductColorRepository $colorRepository,
        private readonly ProductOptionsRepository $optionsRepository,
        private readonly TagsRepository $tagsRepository
    ) {}

    public function collect(string $locale, string $country): array
    {
        $sizes = $this->sizeRepository->getForOptions();
        $prices = $this->optionsRepository->getLowestAndHighestPrice($country);

        return [
            'sizes' => $sizes,
            'colors' => $this->colorRepository->getByLocale($locale),
            'collection' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_COLLECTION, $locale),
            'season' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_SEASON, $locale),
            'attributes' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_ATTRIBUTE, $locale),
            'price' => $prices[0],
        ];
    }
}
