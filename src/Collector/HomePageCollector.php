<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\User;
use App\Repository\BannerRepository;
use App\Repository\CategoryRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
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
     * @var TagsRepository
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
     * @param TagsRepository         $tagsRepository
     */
    public function __construct(
        SliderRepository $sliderRepository,
        BannerRepository $bannerRepository,
        ProductRepository $productRepository,
        ProductColorRepository $colorRepository,
        ProductSizeRepository $sizeRepository,
        TagsRepository $tagsRepository
    ) {
        $this->sliderRepository = $sliderRepository;
        $this->bannerRepository = $bannerRepository;
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
    public function collect(string $locale, ?User $user): array
    {
        $sliders = $this->sliderRepository->getRandomActiveSlider($locale);
        $banners = $this->bannerRepository->getActiveOrderByPosition($locale);
        $products = $this->productRepository->getForHomePage($locale, $user);

        $productIds = array_column($products, 'id');

        $productColors = $this->colorRepository->getByProducts($productIds, $locale);
        $productSizes = $this->sizeRepository->getByProducts($productIds);
        $productTags = $this->tagsRepository->getByProducts($productIds, $locale);

        return [
            'sliders'           => $sliders,
            'banners'           => $banners,
            'products'          => $products,
            'product_colors'    => $productColors,
            'product_sizes'     => $productSizes,
            'product_tags'      => $productTags,
        ];
    }
}
