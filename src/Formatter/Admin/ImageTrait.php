<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use Doctrine\Common\Collections\Collection;
use Exception;
use SiteBundle\Entity\Media;
use Symfony\Component\Routing\RouterInterface;

trait ImageTrait
{
    
    private function imagesFormatter(RouterInterface $router, array $images, string $entity, string $filter = 'tmp_image_thumb'): array
    {
         return array_map(function ($image) use ($router, $entity, $filter) {
            $image['file'] = $router->generate('app.image_show', ['entity' => $entity, 'name' => $image['fileName'], 'filter' => $filter]);

            return $image;
        }, $images);
    }
}