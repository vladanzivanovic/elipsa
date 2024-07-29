<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Banner;
use App\Entity\ProductOptions;
use App\Repository\BannerRepository;
use App\Repository\ProductRepository;
use App\Repository\SliderRepository;

final class HomePageCollector
{
    public function __construct(
        private readonly SliderRepository $sliderRepository,
        private readonly BannerRepository $bannerRepository,
        private readonly ProductRepository $productRepository,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function collect(string $host): array
    {
        $sliders = $this->sliderRepository->getRandomActiveSlider($host);
        $banners[Banner::TYPE_SPEED_LINKS] = $this->bannerRepository->getActiveByType(Banner::TYPE_SPEED_LINKS, $host);
        $banners[Banner::TYPE_LOYALTY] = $this->bannerRepository->getActiveByType(Banner::TYPE_LOYALTY, $host);
        $products = [
            ProductOptions::HOME_PAGE_UP => $this->productRepository->getForHomePage(ProductOptions::HOME_PAGE_UP, $host),
            ProductOptions::HOME_PAGE_DOWN => $this->productRepository->getForHomePage(ProductOptions::HOME_PAGE_DOWN, $host),
        ];

        return [
            'sliders' => $sliders,
            'banners' => $banners,
            'products' => $products,
        ];
    }
}
