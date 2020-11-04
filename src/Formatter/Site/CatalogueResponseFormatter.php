<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use Symfony\Component\Routing\RouterInterface;

final class CatalogueResponseFormatter
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(
        RouterInterface $router
    ) {

        $this->router = $router;
    }
    public function formatResponse(array $catalogues)
    {
        return array_map(function ($catalog) {
            $catalog['image_link'] = $this->router->generate('app.image_show', ['entity' => 'catalog', 'name' => $catalog['imageName'], 'filter' => 'list_thumb']);

            return $catalog;
        }, $catalogues);
    }
}