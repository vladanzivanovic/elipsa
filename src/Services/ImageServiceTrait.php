<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;

trait ImageServiceTrait
{
    /**
     * @param array $images
     *
     * @return void
     */
    public function deleteImages(array $images): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        foreach ($images as $image) {
            /** @var Image $imageObj */
            $imageObj = $this->imageRepository->find($image['id']);

            $this->img->deleteImage($this->img->setFileObject(['file' => $rootDir.$imageDir.$imageObj->getName(), 'fileName' => $imageObj->getName()]));
        }
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private function validateMainImage(array $data): bool
    {
        foreach ($data as $image) {
            if (true === !!$image['isMain']) {
                return true;
            }
        }

        return false;
    }
}