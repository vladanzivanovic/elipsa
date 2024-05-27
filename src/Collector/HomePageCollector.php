<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Banner;
use App\Entity\User;
use App\Repository\BannerRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use App\Repository\SliderRepository;

final class HomePageCollector
{
    private SliderRepository $sliderRepository;

    private BannerRepository $bannerRepository;

    private ProductRepository $productRepository;

    public function __construct(
        SliderRepository $sliderRepository,
        BannerRepository $bannerRepository,
        ProductRepository $productRepository
    ) {
        $this->sliderRepository = $sliderRepository;
        $this->bannerRepository = $bannerRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @return array<string, mixed>
     */
    public function collect(string $locale, string $host): array
    {
        $sliders = $this->sliderRepository->getRandomActiveSlider($host);
        $banners[Banner::TYPE_SPEED_LINKS] = $this->bannerRepository->getActiveByType(Banner::TYPE_SPEED_LINKS, $host);
        $banners[Banner::TYPE_LOYALTY] = $this->bannerRepository->getActiveByType(Banner::TYPE_LOYALTY, $host);
        $products = $this->productRepository->getForHomePage($host);

        return [
            'sliders' => $sliders,
            'banners' => $banners,
            'products' => $products,
        ];
    }
}
