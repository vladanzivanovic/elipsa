<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Image;
use App\Entity\ProductHasImages;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ImageView
{
    private RouterInterface $router;

    public function __construct(
        RouterInterface $router
    ) {

        $this->router = $router;
    }

    public function editProductView(ProductHasImages $productHasImages, string $entity, string $filter = 'tmp_image_thumb'): array
    {
        $image = $productHasImages->getImage();

        $imageArray = $this->view($image, $entity, $filter);
        $imageArray['color_id'] = $productHasImages->getColor()->getId();

        return $imageArray;
    }

    public function productPageView(ProductHasImages $productHasImages): array
    {
        $image = $productHasImages->getImage();

        $imageView = $this->editProductView($productHasImages, 'product', 'product_image');
        $imageView['file_thumb'] = $this->generateImageLink($image->getName(), 'product', 'product_image_thumb');
        $imageView['file_full'] = $this->generateImageLink($image->getName(), 'product', 'product_image_full');

        return $imageView;
    }

    public function view(Image $image, string $entity, string $filter = null): array
    {
        $filter = $filter ?? 'tmp_image_thumb';

        $link = $this->generateImageLink($image->getName(), $entity, $filter);

        return [
            'id' => $image->getId(),
            'fileName' => $image->getName(),
            'isMain' => $image->getIsMain(),
            'file' => $link,
        ];
    }

    private function generateImageLink(string $name, string $entity, string $filter): string
    {
        return $this->router->generate(
            'app.image_show',
            ['entity' => $entity, 'name' => $name, 'filter' => $filter],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
