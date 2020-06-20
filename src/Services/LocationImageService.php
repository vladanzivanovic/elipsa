<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Image;
use App\Entity\Location;
use App\Entity\LocationHasImages;
use App\Entity\LocationTranslation;
use App\Entity\Product;
use App\Entity\ProductHasImages;
use App\Entity\ProductTranslation;
use App\Repository\ImageRepository;
use App\Repository\LocationHasImagesRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductHasImagesRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class LocationImageService
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
     * @var LocationHasImagesRepository
     */
    private $hasImagesRepository;

    /**
     * @param ImageService                $imageService
     * @param ParameterBagInterface       $bag
     * @param ImageRepository             $imageRepository
     * @param LocationHasImagesRepository $hasImagesRepository
     */
    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $bag,
        ImageRepository $imageRepository,
        LocationHasImagesRepository $hasImagesRepository
    ) {
        $this->img = $imageService;
        $this->imageRepository = $imageRepository;
        $this->bag = $bag;
        $this->hasImagesRepository = $hasImagesRepository;
    }

    /**
     * @param Location $location
     * @param array    $data
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function setImages(Location $location, array $data): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $tmpDir = $this->bag->get('upload_tmp_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        $locationTranslation = $location->getByLocale('rs');

        if(empty(array_filter($data))) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

        $slug = Urlizer::transliterate($locationTranslation->getTitle());
        $exceptions = [];

        foreach ($data as $index => $image) {
            if (isset($image['id'])) {
                $imageObj = $this->imageRepository->find($image['id']);
                $hasImage = $this->hasImagesRepository->findOneBy(['location' => $location, 'image' => $imageObj]);

                if(isset($image['deleted']) && true === $image['deleted']) {
                    $image['file'] = $rootDir.$imageDir.$imageObj->getOriginalName();
                    $file = $this->img->setFileObject($image);
                    $imageObj->setFile($file);
                    $imageObj->setIsDeleted(true);

                    $this->hasImagesRepository->delete($hasImage);
                    $this->imageRepository->delete($imageObj);

                    continue;
                }

                if (true === $image['isMain']) {
                    $this->updateImage($location, $imageObj);
                }

                continue;
            }

            try {
                $image['file'] = $rootDir.$tmpDir.$image['fileName'];
                $file = $this->img->setFileObject($image);
            } catch (FileNotFoundException $exception) {
                $exceptions[] = $image['fileName'];

                continue;
            }

            $mediaObj = new Image();

            $image['file'] = $rootDir.$tmpDir.$image['fileName'];

            if (!($file instanceof UploadedFile)) {
                continue;
            }

            $newName = md5($file->getFilename().$slug).'.'.$file->guessExtension();

            $mediaObj->setRelatedToType(Image::RELATED_TYPE_LOCATION);
            $mediaObj->setName($slug.'-'.++$index);
            $mediaObj->setIsmain($image['isMain']);
            $mediaObj->setOriginalName($newName);
            $mediaObj->setFile($file);
            $mediaObj->setDevice(Image::DEVICE_DESKTOP);

            $this->imageRepository->persist($mediaObj);

            $hasImages = new LocationHasImages();
            $hasImages->setLocation($location);
            $hasImages->setImage($mediaObj);

            $location->addLocationHasImage($hasImages);
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
     * @param Location $location
     * @param Image    $image
     *
     * @return void
     */
    private function updateImage(Location $location, Image $image): void
    {
        $images = $this->imageRepository->getLocationImages($location);

        /** @var Image $image */
        foreach ($images as $img) {
            $img->setIsMain(false);
        }

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