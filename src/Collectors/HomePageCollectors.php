<?php

declare(strict_types=1);

namespace App\Collectors;

use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SliderRepository;

final class HomePageCollectors
{
    /**
     * @var SliderRepository
     */
    private $sliderRepository;
    /**
     * @var BannerRepository
     */
    private $bannerRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * HomePageCollectors constructor.
     *
     * @param SliderRepository   $sliderRepository
     * @param BannerRepository   $bannerRepository
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository  $productRepository
     */
    public function __construct(
        SliderRepository $sliderRepository,
        BannerRepository $bannerRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository
    ) {
        $this->sliderRepository = $sliderRepository;
        $this->bannerRepository = $bannerRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    public function collect(string $locale): array
    {
        $sliders = $this->sliderRepository->getActiveOrderByPosition($locale);
        $banners = $this->bannerRepository->getActiveOrderByPosition($locale);
        $categories = $this->categoryRepository->getHomePageCategories($locale);
        $products = $this->productRepository->getForHomePage(array_column($categories, 'id'), $locale);

        return [
            'sliders' => $sliders,
            'banners' => $banners,
            'categories' => $categories,
            'products' => $products,
        ];
    }
}
