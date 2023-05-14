<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Banner;
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
    private SliderRepository $sliderRepository;

    private BannerRepository $bannerRepository;

    private ProductRepository $productRepository;

    private ProductColorRepository $colorRepository;

    private ProductSizeRepository $sizeRepository;

    private TagsRepository $tagsRepository;

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
     * @return array<string, mixed>
     */
    public function collect(string $locale, ?User $user): array
    {
        $sliders = $this->sliderRepository->getRandomActiveSlider($locale);
        $banners = $this->bannerRepository->getActiveByType(Banner::TYPE_SPEED_LINKS);
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
