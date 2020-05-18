<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

final class LocationPageResponseFormatter
{
    use FormatterTrait;

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
        foreach ($data['locations'] as &$location) {
            $images = explode(',', $location['images']);

            $location['images'] = [];

            foreach ($images as $image) {
                $tmp['image_link'] = $this->router->generate('app.image_show', ['name' => $image, 'filter' => 'product_image']);
                $tmp['image_link_thumb'] = $this->router->generate('app.image_show', ['name' => $image, 'filter' => 'product_image_thumb']);
                $tmp['name'] = $image;

                $location['images'][] = $tmp;
            }

            $location['short_description'] = str_replace(["\r\n", PHP_EOL], '<br>', $location['short_description']);

            $location['coordinates'] = [$location['lat'], $location['lng']];
        }

        return $data;
    }
}