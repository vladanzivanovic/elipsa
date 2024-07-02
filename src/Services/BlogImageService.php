<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\BlogHasImages;
use App\Entity\BlogTranslation;
use App\Entity\Image;
use App\Parser\ImageParser;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class BlogImageService
{
    use MainImageValidationTrait;

    public function __construct(
        private readonly ImageRepository $imageRepository,
        private readonly ImageParser $imageParser
    ) {}

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function setImages(BlogTranslation $blogTranslation, array $data): void
    {
        $blog = $blogTranslation->getBlog();

        if (array_filter($data) === []) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

        $exceptions = [];

        foreach ($data as $payload) {

            try {
                $image = $this->imageParser->parse($payload);
                $image->setRelatedToType(Image::RELATED_TYPE_BLOG);

                if (isset($payload['id'])) {
                    $image = $this->imageRepository->find($payload['id']);

                    if (isset($payload['deleted']) && 'true' === $payload['deleted']) {
                        $this->imageParser->delete($image);

                        continue;
                    }
                }

                $blog->setImage($image);
            } catch (FileNotFoundException $exception) {
                $exceptions[] = $exception->getMessage();

                continue;
            }
        }

        if ($exceptions !== []) {
            throw new BadRequestHttpException(json_encode(['images' => $exceptions]));
        }
    }
}
