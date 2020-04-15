<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\Slider;
use App\Helper\ConstantsHelper;
use Symfony\Component\Routing\RouterInterface;

final class HomePageResponseFormatter
{
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
            $slider['image_link'] = $this->router->generate('app.image_show', ['name' => $slider['image'], 'filter' => 'site_slider']);
            $slider['position'] = ConstantsHelper::getConstantName((string) $slider['position'], 'POSITION', Slider::class);

            return $slider;
        }, $data['sliders']);

        $data['banners'] = $this->formatBanners($data['banners']);
        $data['products'] = $this->formatProducts($data['categories'], $data['products']);

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
            $banner['image_link'] = $this->router->generate('app.image_show', ['name' => $banner['image'], 'filter' => $filter]);

            $formattedBanners[$banner['position']] = $banner;
        }

        return $formattedBanners;
    }

    /**
     * @param array $categories
     * @param array $products
     *
     * @return array
     */
    private function formatProducts(array $categories, array $products): array 
    {
        $formattedProducts = [];

        foreach ($categories as $category) {
            foreach ($products as $product) {
                $categoryArray = explode(',', $product['categories']);

                if (in_array($category['id'], $categoryArray)) {
                    $product['image_link'] = $this->router->generate('app.image_show', ['name' => $product['image'], 'filter' => 'list_thumb']);

                    $formattedProducts[$category['slug']][] = $product;
                }
            }
        }

        return $formattedProducts;
    }
}