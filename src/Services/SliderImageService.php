<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;
use App\Entity\Slider;
use App\Parser\ImageParser;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class SliderImageService
{
    use MainImageValidationTrait;

    protected ImageService $img;

    private ImageParser $imageParser;

    public function __construct(
        ImageService $imageService,
        ImageParser $imageParser
    ) {
        $this->img = $imageService;
        $this->imageParser = $imageParser;
    }

    public function setImages(Slider $slider, array $data, string $device): void
    {
        if(array_filter($data) === []) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

        $exceptions = [];

        foreach ($data as $payload) {
            try {
                $image = $this->imageParser->parse($payload, $device);
                $image->setRelatedToType(Image::RELATED_TYPE_SLIDER);

                if ($device === Image::DEVICE_MOBILE) {
                    $image->setParentImage($slider->getImage()->getName());
                }

                if (!empty($payload['id']) && (isset($payload['deleted']) && 'true' === $payload['deleted'])) {
                    $this->imageParser->delete($image);
                    continue;
                }

                if ($device === Image::DEVICE_DESKTOP) {
                    $slider->setImage($image);
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
