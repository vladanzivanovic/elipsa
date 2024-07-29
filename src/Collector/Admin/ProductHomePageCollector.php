<?php

declare(strict_types=1);

namespace App\Collector\Admin;

use App\Entity\ProductOptions;
use App\Repository\ProductOptionsRepository;

final class ProductHomePageCollector
{
    public function __construct(
        private readonly ProductOptionsRepository $productOptionsRepository,
        private readonly array $countries,
    ) {}

    /**
     * @return array<string, array<string, array<int, ProductOptions>>>
     */
    public function collect(): array
    {
        $collection = [];

        foreach ($this->countries as $countryCode => $country) {
            $collection[ProductOptions::HOME_PAGE_UP][$countryCode] = $this->productOptionsRepository->getProductsHasHomePagePosition(ProductOptions::HOME_PAGE_UP, $countryCode);

            $collection[ProductOptions::HOME_PAGE_DOWN][$countryCode] = $this->productOptionsRepository->getProductsHasHomePagePosition(ProductOptions::HOME_PAGE_DOWN, $countryCode);
        }

        return $collection;
    }
}
