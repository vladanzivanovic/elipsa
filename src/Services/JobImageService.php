<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CareerDescriptionTranslation;
use App\Entity\Image;
use App\Parser\ImageParser;
use App\Repository\ImageRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class JobImageService
{
    use MainImageValidationTrait;

    public function __construct(
        private readonly ImageService $imageService,
        private readonly ParameterBagInterface $bag,
        private readonly ImageRepository $imageRepository,
        private readonly ImageParser $imageParser,
    ) {}

    public function setImages(CareerDescriptionTranslation $careerDescriptionTranslation, array $data): void
    {
        $careerDescription = $careerDescriptionTranslation->getCareerDescription();

        if (array_filter($data) === []) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

        $exceptions = [];

        foreach ($data as $payload) {
            try {
                $image = $this->imageParser->parse(
                    $payload,
                    Image::DEVICE_DESKTOP,
                    false,
                    Image::RELATED_TYPE_CAREER
                );

                if (!empty($payload['id']) && (isset($payload['deleted']) && 'true' === $payload['deleted'])) {
                    $this->imageParser->delete($image);
                    continue;
                }

                $careerDescription->setImage($image);
            } catch (\Throwable $throwable) {
                $exceptions[] = $throwable->getMessage();
            }
        }

        if ($exceptions !== []) {
            throw new BadRequestHttpException(json_encode(['images' => $exceptions]));
        }
    }
}
