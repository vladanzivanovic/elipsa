<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use Doctrine\Common\Collections\Collection;
use Exception;
use SiteBundle\Entity\Media;
use Symfony\Component\Routing\RouterInterface;

trait ImageTrait
{
    /**
     * @param RouterInterface $router
     * @param array           $images
     *
     * @return array
     */
    private function imagesFormatter(RouterInterface $router, array $images): array
    {
         return array_map(function ($image) use ($router) {
            $image['file'] = $router->generate('app.image_show', ['name' => $image['fileName'], 'filter' => 'tmp_image_thumb']);

            return $image;
        }, $images);
    }
}