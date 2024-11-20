<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;
use App\Entity\OrderProduct;
use App\Entity\ProductHasImages;
use App\Entity\ProductTranslation;
use App\Parser\ImageParser;
use App\Repository\ProductColorRepository;
use App\Repository\ProductHasImagesRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class ProductImageService
{
    use MainImageValidationTrait;

    private ProductColorRepository $colorRepository;

    private ProductHasImagesRepository $hasImagesRepository;

    private ImageParser $imageParser;

    public function __construct(
        ProductColorRepository $colorRepository,
        ProductHasImagesRepository $hasImagesRepository,
        ImageParser $imageParser
    ) {
        $this->colorRepository = $colorRepository;
        $this->hasImagesRepository = $hasImagesRepository;
        $this->imageParser = $imageParser;
    }

    public function setImages(ProductTranslation $productTranslation, array $data, bool $fromImport = false): void
    {
        $product = $productTranslation->getProduct();

        if(array_filter($data) === []) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

        $exceptions = [];

        foreach ($data as $payload) {
            try {
                $color = $this->colorRepository->find($payload['color_id']);

                $payload['fileName'] = $payload['fileName'] ?? $payload['file_name']; // todo fix this

                $image = $this->imageParser->parse($payload);

                if (isset($payload['id'])) {
                    $hasImage = $this->hasImagesRepository->findOneBy(['product' => $product, 'image' => $image]);
                    $hasImage->setColor($color);

                    if (isset($payload['deleted']) && 'true' === $payload['deleted']) {
                        $orderImages = $product->getOrderProducts()->filter(function (OrderProduct $orderProduct) use ($image) {
                            return $orderProduct->getImage() === $image;
                        });

                        $this->hasImagesRepository->delete($hasImage);

                        if (0 === count($orderImages)) {
                            $this->imageParser->delete($image);
                        }
                    }
                }

                if (!isset($payload['id'])) {
                    $hasImages = new ProductHasImages();
                    $hasImages->setProduct($product);
                    $hasImages->setImage($image);
                    $hasImages->setColor($color);

                    $product->addProductHasImage($hasImages);
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
