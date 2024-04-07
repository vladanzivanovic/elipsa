<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Catalogue;
use MobileDetectBundle\DeviceDetector\MobileDetectorInterface;

final class CatalogView
{
    private ImageView $imageView;

    private MobileDetectorInterface $mobileDetect;

    public function __construct(
        ImageView $imageView,
        MobileDetectorInterface $mobileDetect
    ) {

        $this->imageView = $imageView;
        $this->mobileDetect = $mobileDetect;
    }

    public function view(Catalogue $catalogue, string $locale): array
    {
        $trans = $catalogue->getByLocale($locale);

        return [
            'title' => $trans->getTitle(),
            'slug' => $trans->getSlug(),
            'images' => $this->getImages($catalogue),
        ];
    }

    private function getImages(Catalogue $catalogue): array
    {
        $images = [];

        $fullFilter = true === $this->mobileDetect->isMobile() ? 'full_mobile' : 'full';

        foreach ($catalogue->getCatalogueHasImages() as $catalogueHasImage) {
            $image = $catalogueHasImage->getImage();

            $images['all'][] = [
                'thumb' => $this->imageView->view($image, 'catalog', 'list_thumb'),
                'full' => $this->imageView->view($image, 'catalog', $fullFilter),
            ];

            if (true === $image->getIsMain()) {
                $images['main'] = [
                    'thumb' => $this->imageView->view($image, 'catalog', 'list_thumb'),
                    'full' => $this->imageView->view($image, 'catalog', $fullFilter),
                ];
            }
        }

        return $images;
    }
}
