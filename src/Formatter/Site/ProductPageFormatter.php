<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use Symfony\Component\Routing\RouterInterface;

final class ProductPageFormatter
{
    use FormatterTrait;
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * ProductPageFormatter constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {
        $this->router = $router;
    }

    public function formatResponse(array $data): array
    {
        foreach ($data['images'] as &$image) {
            $image['image_link'] = $this->router->generate('app.image_show', ['entity' => 'product', 'name' => $image['fileName'], 'filter' => 'product_image']);
            $image['image_link_thumb'] = $this->router->generate('app.image_show', ['entity' => 'product', 'name' => $image['fileName'], 'filter' => 'product_image_thumb']);
            $tmpArray['name'] = $image['fileName'];
        }

        $data['related_products']['products'] = array_map(function ($product) {

            $product['image_link_list'] = $this->router->generate('app.image_show', ['entity' => 'product', 'name' => $product['image'], 'filter' => 'list_thumb']);

            return $product;
        }, $data['related_products']['products']);

        $data['related_products']['product_colors'] = $this->formatColors($data['related_products']['product_colors']);
        $data['related_products']['product_sizes'] = $this->formatSizes($data['related_products']['product_sizes']);
        $data['related_products']['product_tags'] = $this->formatTags($data['related_products']['product_tags']);

        return $data;
    }
}