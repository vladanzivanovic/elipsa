<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Catalogue;
use App\Entity\CatalogueHasImages;
use App\Entity\CatalogueTranslation;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\ProductHasImages;
use App\Entity\ProductTranslation;
use App\Repository\CatalogueHasImagesRepository;
use App\Repository\CatalogueRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductHasImagesRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class CatalogImageService
{
    use ImageServiceTrait;

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
     * @var CatalogueHasImagesRepository
     */
    private $hasImagesRepository;

    /**
     * @param ImageService                 $imageService
     * @param ParameterBagInterface        $bag
     * @param ImageRepository              $imageRepository
     * @param CatalogueHasImagesRepository $hasImagesRepository
     */
    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $bag,
        ImageRepository $imageRepository,
        CatalogueHasImagesRepository $hasImagesRepository
    ) {
        $this->img = $imageService;
        $this->imageRepository = $imageRepository;
        $this->bag = $bag;
        $this->hasImagesRepository = $hasImagesRepository;
    }

    /**
     * @param CatalogueTranslation $catalogueTranslation
     * @param array                $data
     *
     * @param bool                 $fromImport
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function setImages(CatalogueTranslation $catalogueTranslation, array $data, bool $fromImport = false): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $tmpDir = true === $fromImport ? $this->bag->get('upload_import_dir') : $this->bag->get('upload_tmp_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        $catalogue = $catalogueTranslation->getCatalogue();

        if(empty(array_filter($data))) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

        $slug = Urlizer::transliterate($catalogueTranslation->getTitle());
        $exceptions = [];

        foreach ($data as $index => $image) {

            if (isset($image['id'])) {
                $imageObj = $this->imageRepository->find($image['id']);
                $hasImage = $this->hasImagesRepository->findOneBy(['catalogue' => $catalogue, 'image' => $imageObj]);

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
                    $this->updateImage($catalogue, $imageObj);
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

            $mediaObj->setRelatedToType(Image::RELATED_TYPE_PRODUCT);
            $mediaObj->setName($slug.'-'.++$index);
            $mediaObj->setIsmain($image['isMain']);
            $mediaObj->setOriginalName($newName);
            $mediaObj->setFile($file);
            $mediaObj->setDevice(Image::DEVICE_DESKTOP);

            $this->imageRepository->persist($mediaObj);

            $hasImages = new CatalogueHasImages();
            $hasImages->setCatalogue($catalogue);
            $hasImages->setImage($mediaObj);

            $catalogue->addCatalogueHasImage($hasImages);
        }

        if (count($exceptions) > 0) {
            throw new BadRequestHttpException(json_encode(['images' => $exceptions]));
        }
    }

    /**
     * @param Catalogue $catalogue
     * @param Image     $image
     *
     * @return void
     */
    private function updateImage(Catalogue $catalogue, Image $image): void
    {
        $images = $this->imageRepository->getCatalogImages($catalogue);

        /** @var Image $image */
        foreach ($images as $img) {
            $img->setIsMain(false);
        }

        $image->setIsMain(true);
    }
}