<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Banner;
use App\View\BannerView;
use App\View\SliderView;
use Symfony\Component\Routing\RouterInterface;

final class HomePageResponseFormatter
{
    use FormatterTrait;

    private RouterInterface $router;

    private SliderView $sliderView;

    private BannerView $bannerView;

    private ProductFormatter $productFormatter;

    public function __construct(
        RouterInterface $router,
        SliderView $sliderView,
        BannerView $bannerView,
        ProductFormatter $productFormatter
    ) {
        $this->router = $router;
        $this->sliderView = $sliderView;
        $this->bannerView = $bannerView;
        $this->productFormatter = $productFormatter;
    }

    public function formatResponse(array $data, string $locale): array
    {
        $data['sliders'] = array_map(function ($slider) use ($locale) {

            return $this->sliderView->siteView($slider, $locale);
        }, $data['sliders']);

        $data['banners'] = $this->formatBanners($data['banners'], $locale);
        $data['products'] = $this->formatProducts(
            $this->productFormatter->getProducts($data['products'], $locale)
        );

        return $data;
    }

    /**
     * @param Banner[] $banners
     *
     * @return array
     */
    private function formatBanners(array $banners, string $locale): array
    {
        $formattedBanners = [];

        foreach ($banners as $banner) {
            $formattedBanners[$banner->getPosition()] = $this->bannerView->speedLinks($banner, $locale);
        }

        return $formattedBanners;
    }

    /**
     * @param array $products
     *
     * @return array
     */
    private function formatProducts(array $products): array
    {
        $formattedProducts = [];

        foreach ($products as $product){
            $formattedProducts[$product['show_home_page']][] = $product;
        }

        return $formattedProducts;
    }
}
