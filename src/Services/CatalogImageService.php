<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CatalogueHasImages;
use App\Entity\CatalogueTranslation;
use App\Entity\Image;
use App\Parser\ImageParser;
use App\Repository\CatalogueHasImagesRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class CatalogImageService
{
    use MainImageValidationTrait;

    protected ImageService $img;

    private CatalogueHasImagesRepository $hasImagesRepository;

    private ImageParser $imageParser;

    public function __construct(
        ImageService $imageService,
        CatalogueHasImagesRepository $hasImagesRepository,
        ImageParser $imageParser
    ) {
        $this->img = $imageService;
        $this->hasImagesRepository = $hasImagesRepository;
        $this->imageParser = $imageParser;
    }

    public function setImages(CatalogueTranslation $catalogueTranslation, array $data): void
    {
        $catalogue = $catalogueTranslation->getCatalogue();

        if(array_filter($data) === []) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

        $exceptions = [];

        foreach ($data as $payload) {
            try {
                $image = $this->imageParser->parse($payload);

                if (isset($payload['id'])) {
                    $hasImage = $this->hasImagesRepository->findOneBy(['catalogue' => $catalogue, 'image' => $image]);

                    if (isset($payload['deleted']) && true === $payload['deleted']) {
                        $this->hasImagesRepository->delete($hasImage);
                        $this->imageParser->delete($image);

                        continue;
                    }
                }

                if (!isset($payload['id'])) {
                    $hasImages = new CatalogueHasImages();
                    $hasImages->setCatalogue($catalogue);
                    $hasImages->setImage($image);

                    $catalogue->addCatalogueHasImage($hasImages);
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
