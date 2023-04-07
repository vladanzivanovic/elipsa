<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\View\SliderView;
use Symfony\Component\Routing\RouterInterface;

final class HomePageResponseFormatter
{
    use FormatterTrait;

    private RouterInterface $router;

    private SliderView $sliderView;

    public function __construct(
        RouterInterface $router,
        SliderView $sliderView
    ) {
        $this->router = $router;
        $this->sliderView = $sliderView;
    }

    public function formatResponse(array $data, string $locale): array
    {
        $data['sliders'] = array_map(function ($slider) use ($locale) {

            return $this->sliderView->siteView($slider, $locale);
//            $slider['description'] = explode(PHP_EOL, $slider['description']);
//            preg_match('%(<p[^>]*>.*?</p>)%i', $slider['description'], $matcher);

//            $slider['description'] = $matcher;
//            $slider['image_link'] = $this->router->generate('app.image_show', ['entity' => 'slider', 'name' => $slider['image'], 'filter' => 'site_slider']);
//            $slider['mobile_image_link'] = $this->router->generate('app.image_show', ['entity' => 'slider', 'name' => $slider['mobile_image'], 'filter' => 'site_slider_mobile']);
//            $slider['position'] = ConstantsHelper::getConstantName((string) $slider['position'], 'POSITION', Slider::class);

//            return $slider;
        }, $data['sliders']);

        $data['banners'] = $this->formatBanners($data['banners']);
        $data['products'] = $this->formatProducts($data['products']);

        $data['product_colors'] = $this->formatColors($data['product_colors']);
        $data['product_sizes'] = $this->formatSizes($data['product_sizes']);
        $data['product_tags'] = $this->formatTags($data['product_tags']);

        return $data;
    }

    /**
     * @param array $banners
     *
     * @return array
     */
    private function formatBanners(array $banners): array
    {
        $formattedBanners = [];

        foreach ($banners as $banner) {
            $filter = in_array($banner['position'], [1,4]) ? 'home_banner_side' : 'home_banner_center';
            $banner['description'] = explode(PHP_EOL, $banner['description']);
            $banner['image_link'] = $this->router->generate('app.image_show', ['entity' => 'banner', 'name' => $banner['image'], 'filter' => $filter]);

            $formattedBanners[$banner['position']] = $banner;
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
            $product['image_link_list'] = $this->router->generate('app.image_show', ['entity' => 'product', 'name' => $product['image'], 'filter' => 'list_thumb']);

            $formattedProducts[$product['show_home_page']][] = $product;
        }

        return $formattedProducts;
    }
}
