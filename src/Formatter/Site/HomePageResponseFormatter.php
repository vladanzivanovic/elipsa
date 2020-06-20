<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use Symfony\Component\Routing\RouterInterface;

final class HomePageResponseFormatter
{
    use FormatterTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * HomePageResponseFormatter constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function formatResponse(array $data): array
    {
        $data['sliders'] = array_map(function ($slider) {
            $slider['description'] = explode(PHP_EOL, $slider['description']);
            $slider['image_link'] = $this->router->generate('app.image_show', ['entity' => 'slider', 'name' => $slider['image'], 'filter' => 'site_slider']);
            $slider['mobile_image_link'] = $this->router->generate('app.image_show', ['entity' => 'slider', 'name' => $slider['mobile_image'], 'filter' => 'site_slider']);
            $slider['position'] = ConstantsHelper::getConstantName((string) $slider['position'], 'POSITION', Slider::class);

            return $slider;
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