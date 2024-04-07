<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;
use App\Entity\Location;
use App\Entity\LocationHasImages;
use App\Parser\ImageParser;
use App\Repository\LocationHasImagesRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class LocationImageService
{
    use MainImageValidationTrait;

    private LocationHasImagesRepository $hasImagesRepository;

    private ImageParser $imageParser;

    public function __construct(
        LocationHasImagesRepository $hasImagesRepository,
        ImageParser $imageParser
    ) {
        $this->hasImagesRepository = $hasImagesRepository;
        $this->imageParser = $imageParser;
    }

    public function setImages(Location $location, array $data): void
    {
        if(array_filter($data) === []) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

        $exceptions = [];

        foreach ($data as $payload) {
            try {
                $payload['fileName'] = $payload['fileName'] ?? $payload['file_name']; //f todo fix this
                $payload['isMain'] = $payload['isMain'] ?? $payload['is_main'];

                $image = $this->imageParser->parse(
                    $payload,
                    Image::DEVICE_DESKTOP,
                    false,
                    Image::RELATED_TYPE_LOCATION
                );

                if (isset($payload['id'])) {
                    $hasImage = $this->hasImagesRepository->findOneBy(['location' => $location, 'image' => $image]);

                    if(isset($payload['deleted']) && true === $payload['deleted']) {
                        $this->hasImagesRepository->delete($hasImage);
                        $this->imageParser->delete($image);
                    }
                }

                if (!isset($payload['id'])) {
                    $hasImages = new LocationHasImages();
                    $hasImages->setLocation($location);
                    $hasImages->setImage($image);

                    $location->addLocationHasImage($hasImages);
                }
            } catch (\Throwable $throwable) {
                $exceptions[] = $throwable->getMessage();
            }
        }

        if ($exceptions !== []) {
            throw new BadRequestHttpException(json_encode(['images' => $exceptions]));
        }
    }
}
