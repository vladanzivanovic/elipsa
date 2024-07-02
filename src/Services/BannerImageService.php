<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Banner;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Slider;
use App\Repository\ImageRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class BannerImageService
{
    protected \App\Services\ImageService $img;

    private \App\Repository\ImageRepository $imageRepository;

    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $bag;

    /**
     * BannerImageService constructor.
     */
    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $bag,
        ImageRepository $imageRepository
    ) {
        $this->img = $imageService;
        $this->imageRepository = $imageRepository;
        $this->bag = $bag;
    }

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function setImages(Banner $banner, array $data, string $device): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $tmpDir = $this->bag->get('upload_tmp_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        if (array_filter($data) === []) {
            return;
        }

        foreach ($data as $index => $image) {
            if (!empty($image['id'])) {
                if (isset($image['deleted']) && true === $image['deleted']) {
                    $imageObj = $this->imageRepository->find($image['id']);

                    $image['file'] = $rootDir . $imageDir . $imageObj->getOriginalName();
                    $file = $this->img->setFileObject($image);
                    $imageObj->setFile($file);
                    $imageObj->setIsDeleted(true);

                    $this->imageRepository->delete($imageObj);
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

            $slug = Urlizer::transliterate(md5($file->getFilename()));

            $newName = $slug.'.'.$file->guessExtension();

            $mediaObj->setRelatedToType(Image::RELATED_TYPE_BANNER);
            $mediaObj->setName($slug.'-'.++$index);
            $mediaObj->setIsmain($image['isMain']);
            $mediaObj->setOriginalName($newName);
            $mediaObj->setFile($file);
            $mediaObj->setDevice($device);

            if ($device === Image::DEVICE_MOBILE) {
                $mediaObj->setParentImage($banner->getImage()->getName());
            }

            $this->imageRepository->persist($mediaObj);

            if ($device === Image::DEVICE_DESKTOP) {
                $banner->setImage($mediaObj);
            }
        }
    }

    
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
}
