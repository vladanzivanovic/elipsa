<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

final class ShopPageResponseFormatter
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param RouterInterface       $router
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        RouterInterface $router,
        ParameterBagInterface $bag
    ) {
        $this->router = $router;
        $this->bag = $bag;
    }

    /**
     * @param array<string, array<array<string|int, mixed>>> $data
     *
     * @return array<string, array<array<string|int, mixed>>>
     */
    public function formatResponse(array $data): array
    {
        $sortMapping = $this->bag->get('shop')['sort_mapping'];

        $data['products']['data'] = array_map(function ($product) {

            $product['image_link_list'] = $this->router->generate('app.image_show', ['name' => $product['image'], 'filter' => 'list_thumb']);
            $product['image_link_grid'] = $this->router->generate('app.image_show', ['name' => $product['image'], 'filter' => 'grid_thumb']);

            return $product;
        }, $data['products']['data']);

        $data['product_colors'] = $this->formatColors($data['product_colors']);
        $data['product_sizes'] = $this->formatSizes($data['product_sizes']);
        $data['product_tags'] = $this->formatTags($data['product_tags']);

        if (null !== $data['search_criteria'] && $data['search_criteria']->has('sort')) {
            $data['search_criteria']->set('sort', [array_search($data['search_criteria']->get('sort'), $sortMapping)]);
        }

        return $data;
    }

    /**
     * @param array $productColors
     *
     * @return array
     */
    private function formatColors(array $productColors): array
    {
        $formattedProduct = [];

        foreach ($productColors as $productColor) {
            $formattedProduct[$productColor['productId']][] = $productColor['hex'];
        }

        return $formattedProduct;
    }

    /**
     * @param array $productSizes
     *
     * @return array
     */
    private function formatSizes(array $productSizes): array
    {
        $formattedProduct = [];

        foreach ($productSizes as $productSize) {
            $formattedProduct[$productSize['productId']][] = $productSize['size'];
        }

        return $formattedProduct;
    }

    /**
     * @param array $productTags
     *
     * @return array
     */
    private function formatTags(array $productTags): array
    {
        $formattedProduct = [];

        foreach ($productTags as $productTag) {
            $formattedProduct[$productTag['productId']][] = $productTag['label'];
        }

        return $formattedProduct;
    }
}