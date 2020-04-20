<?php

declare(strict_types=1);

namespace App\Collector;

use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTagsRepository;
use App\Repository\SliderRepository;

final class HomePageCollector
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
     * @var ProductColorRepository
     */
    private $colorRepository;
    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;
    /**
     * @var ProductTagsRepository
     */
    private $tagsRepository;

    /**
     * HomePageCollector constructor.
     *
     * @param SliderRepository       $sliderRepository
     * @param BannerRepository       $bannerRepository
     * @param CategoryRepository     $categoryRepository
     * @param ProductRepository      $productRepository
     * @param ProductColorRepository $colorRepository
     * @param ProductSizeRepository  $sizeRepository
     * @param ProductTagsRepository  $tagsRepository
     */
    public function __construct(
        SliderRepository $sliderRepository,
        BannerRepository $bannerRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        ProductColorRepository $colorRepository,
        ProductSizeRepository $sizeRepository,
        ProductTagsRepository $tagsRepository
    ) {
        $this->sliderRepository = $sliderRepository;
        $this->bannerRepository = $bannerRepository;
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->colorRepository = $colorRepository;
        $this->sizeRepository = $sizeRepository;
        $this->tagsRepository = $tagsRepository;
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

        $productIds = array_column($products, 'id');

        $productColors = $this->colorRepository->getByProducts($productIds, $locale);
        $productSizes = $this->sizeRepository->getByProducts($productIds);
        $productTags = $this->tagsRepository->getByProducts($productIds, $locale);

        return [
            'sliders'           => $sliders,
            'banners'           => $banners,
            'categories'        => $categories,
            'products'          => $products,
            'product_colors'    => $productColors,
            'product_sizes'     => $productSizes,
            'product_tags'      => $productTags,
        ];
    }
}
