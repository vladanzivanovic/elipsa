<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Image;
use App\Entity\Product;
use App\Repository\ImageRepository;
use App\Services\ImageService;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\ORMException;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageParser
{
    private ImageRepository $imageRepository;

    private ImageService $imageService;

    private string $imageDir;

    private string $importDir;

    private string $tmpDir;

    private string $rootDir;

    public function __construct(
        ImageRepository $imageRepository,
        ImageService $imageService,
        ParameterBagInterface $bag
    ) {
        $this->imageRepository = $imageRepository;
        $this->imageService = $imageService;

        $this->rootDir = $bag->get('upload_dir');
        $this->tmpDir = $bag->get('upload_tmp_dir');
        $this->importDir = $bag->get('upload_import_dir');
        $this->imageDir = $bag->get('upload_image_dir');
    }

    /**
     * @throws ORMException
     */
    public function parse(
        array $payload,
        int $device = Image::DEVICE_DESKTOP,
        bool $fromImport = false,
        int $relatedTo = Image::RELATED_TYPE_PRODUCT
    ): Image {
        $image = $this->create();

        $imageName = $payload['fileName'];

        if (isset($payload['id'])) {
            $image = $this->imageRepository->find($payload['id']);

            $imageName = $image->getOriginalName();
        }

        $payload['file'] = $this->getUploadDirPath(null !== $image->getId(), $fromImport) . $imageName;

        $file = $this->setFileObject($payload);

        $image->setFile($file);

        if(isset($payload['deleted']) && 'true' === $payload['deleted']) {
            // don't want to set for removal implicitly, because if product is used by order then we want to keep main image
            return $image;
        }

        $image->setRelatedToType($relatedTo);
        $image->setIsmain('true' === $payload['isMain']);
        $image->setDevice($device);

        $this->setNewImageFileName($file, $image);

        if (null === $image->getId()) {
            $this->imageRepository->persist($image);
        }

        return $image;
    }

    /**
     * @throws ORMException
     */
    public function delete(Image $image): void
    {
        $image->setIsDeleted(true);

        $this->imageRepository->delete($image);
    }

    /**
     * @throws ORMException
     */
    private function create(): Image
    {
        return new Image();
    }

    private function setNewImageFileName($file, Image $image): void
    {
        if (null !== $image->getId()) {
            return;
        }

        $slug = Urlizer::transliterate(md5($file->getFilename()));

        $newName = $slug.'.'.$file->guessExtension();

        $now = new \DateTimeImmutable();

        $image->setName($slug.'-'.$now->getTimestamp());
        $image->setOriginalName($newName);
    }

    private function setFileObject(array $payload): UploadedFile
    {
        return $this->imageService->setFileObject($payload);
    }

    private function getUploadDirPath(bool $isNew, bool $fromImport = false): string
    {
        $imageDir = $this->imageDir;

        if (false === $isNew) {
            $imageDir = $fromImport ? $this->importDir : $this->tmpDir;
        }

        return $this->rootDir.$imageDir;
    }
}
