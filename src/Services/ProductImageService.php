<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;
use App\Entity\Product;
use App\Entity\ProductHasImages;
use App\Entity\ProductTranslation;
use App\Repository\ImageRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductHasImagesRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class ProductImageService
{
    /**
     * @var ImageService
     */
    protected $img;

    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var ParameterBagInterface
     */
    private $bag;
    /**
     * @var ProductColorRepository
     */
    private $colorRepository;
    /**
     * @var ProductHasImagesRepository
     */
    private $hasImagesRepository;

    /**
     * ProductImageService constructor.
     *
     * @param ImageService               $imageService
     * @param ParameterBagInterface      $bag
     * @param ImageRepository            $imageRepository
     * @param ProductColorRepository     $colorRepository
     * @param ProductHasImagesRepository $hasImagesRepository
     */
    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $bag,
        ImageRepository $imageRepository,
        ProductColorRepository $colorRepository,
        ProductHasImagesRepository $hasImagesRepository
    ) {
        $this->img = $imageService;
        $this->imageRepository = $imageRepository;
        $this->bag = $bag;
        $this->colorRepository = $colorRepository;
        $this->hasImagesRepository = $hasImagesRepository;
    }

    /**
     * @param ProductTranslation $productTranslation
     * @param array              $data
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function setImages(ProductTranslation $productTranslation, array $data): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $tmpDir = $this->bag->get('upload_tmp_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        $product = $productTranslation->getProduct();

        if(empty(array_filter($data))) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

        $slug = Urlizer::transliterate($productTranslation->getTitle());
        $exceptions = [];

        foreach ($data as $index => $image) {
            if (isset($image['id'])) {
                $imageObj = $this->imageRepository->find($image['id']);

                if(isset($image['deleted']) && true === $image['deleted']) {
                    $image['file'] = $rootDir.$imageDir.$imageObj->getOriginalName();
                    $file = $this->img->setFileObject($image);
                    $imageObj->setFile($file);
                    $imageObj->setIsDeleted(true);

                    $hasImage = $this->hasImagesRepository->findOneBy(['product' => $product, 'image' => $imageObj]);
                    $this->hasImagesRepository->delete($hasImage);
                    $this->imageRepository->delete($imageObj);

                    continue;
                }

                if (true === $image['isMain']) {
                    $this->updateImage($product, $imageObj);
                }
                continue;
            }

            try {
                $image['file'] = $rootDir.$tmpDir.$image['fileName'];
                $file = $this->img->setFileObject($image);
            } catch (FileNotFoundException $exception) {
                dd($exception);
                $exceptions[] = $image['fileName'];

                continue;
            }

            $mediaObj = new Image();

            $image['file'] = $rootDir.$tmpDir.$image['fileName'];

            if (!($file instanceof UploadedFile)) {
                continue;
            }

            $newName = md5($file->getFilename().$slug).'.'.$file->guessExtension();

            $mediaObj->setRelatedToType(Image::RELATED_TYPE_PRODUCT);
            $mediaObj->setName($slug.'-'.++$index);
            $mediaObj->setIsmain($image['isMain']);
            $mediaObj->setOriginalName($newName);
            $mediaObj->setFile($file);

            $this->imageRepository->persist($mediaObj);

            $hasImages = new ProductHasImages();
            $color = $this->colorRepository->findOneBy(['mainSlug' => $image['color']]);
            $hasImages->setProduct($product);
            $hasImages->setImage($mediaObj);
            $hasImages->setColor($color);

            $product->addProductHasImage($hasImages);
        }

        if (count($exceptions) > 0) {
            throw new BadRequestHttpException(json_encode(['images' => $exceptions]));
        }
    }

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
     * @param Product $product
     * @param Image   $image
     *
     * @return void
     */
    private function updateImage(Product $product, Image $image): void
    {
        $this->imageRepository->removeMainImage($product, Image::RELATED_TYPE_PRODUCT);

        $image->setIsMain(true);
    }

    private function validateMainImage(array $data)
    {
        foreach ($data as $image) {
            if (true === !!$image['isMain']) {
                return true;
            }
        }

        return false;
    }
}