<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Blog;
use App\Entity\BlogHasImages;
use App\Entity\BlogTranslation;
use App\Entity\CareerDescription;
use App\Entity\CareerDescriptionTranslation;
use App\Entity\Image;
use App\Parser\ImageParser;
use App\Repository\BlogHasImagesRepository;
use App\Repository\BlogRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductHasImagesRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class JobImageService
{
    use MainImageValidationTrait;

//    use ImageServiceTrait;

    public function __construct(
        private readonly ImageService $imageService,
        private readonly ParameterBagInterface $bag,
        private readonly ImageRepository $imageRepository,
        private readonly ImageParser $imageParser,
    ) {}

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function setImages(CareerDescriptionTranslation $careerDescriptionTranslation, array $data): void
    {
//        $rootDir = $this->bag->get('upload_dir');
//        $tmpDir = $this->bag->get('upload_tmp_dir');
//        $imageDir = $this->bag->get('upload_image_dir');

        $careerDescription = $careerDescriptionTranslation->getCareerDescription();

        if (array_filter($data) === []) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

//        $slug = Urlizer::transliterate($careerDescriptionTranslation->getTitle());
        $exceptions = [];

        foreach ($data as $payload) {
            try {
                $image = $this->imageParser->parse(
                    $payload,
                    Image::DEVICE_DESKTOP,
                    false,
                    Image::RELATED_TYPE_CAREER
                );
//                $image->setRelatedToType(Image::RELATED_TYPE_SLIDER);

//                if ($device === Image::DEVICE_MOBILE) {
//                    $image->setParentImage($slider->getImage()->getName());
//                }

                if (!empty($payload['id']) && (isset($payload['deleted']) && 'true' === $payload['deleted'])) {
                    $this->imageParser->delete($image);
                    continue;
                }

//                if ($device === Image::DEVICE_DESKTOP) {
                $careerDescription->setImage($image);
//                }
            } catch (\Throwable $throwable) {
                $exceptions[] = $throwable->getMessage();
            }



//            if (isset($image['id'])) {
//                $imageObj = $this->imageRepository->find($image['id']);
//
//                if(isset($image['deleted']) && true === $image['deleted']) {
//                    $image['file'] = $rootDir.$imageDir.$imageObj->getOriginalName();
//                    $file = $this->img->setFileObject($image);
//                    $imageObj->setFile($file);
//                    $imageObj->setIsDeleted(true);
//
//                    $this->imageRepository->delete($imageObj);
//
//                    continue;
//                }
//
//                if (true === $image['isMain']) {
//                    $this->updateImage($career, $imageObj);
//                }
//
//                continue;
//            }

//            try {
//                $image['file'] = $rootDir.$tmpDir.$image['fileName'];
//                $file = $this->img->setFileObject($image);
//            } catch (FileNotFoundException $exception) {
//                $exceptions[] = $image['fileName'];
//
//                continue;
//            }
//
//            $mediaObj = new Image();
//
//            $image['file'] = $rootDir.$tmpDir.$image['fileName'];
//
//            if (!($file instanceof UploadedFile)) {
//                continue;
//            }
//
//            $newName = md5($file->getFilename().$slug).'.'.$file->guessExtension();
//
//            $mediaObj->setRelatedToType(Image::RELATED_TYPE_BLOG);
//            $mediaObj->setName($slug.'-'.++$index);
//            $mediaObj->setIsmain($image['isMain']);
//            $mediaObj->setOriginalName($newName);
//            $mediaObj->setFile($file);
//            $mediaObj->setDevice(Image::DEVICE_DESKTOP);
//
//            $this->imageRepository->persist($mediaObj);
//
//            $career->setImage($mediaObj);
        }

        if ($exceptions !== []) {
            throw new BadRequestHttpException(json_encode(['images' => $exceptions]));
        }
    }

    
    private function updateImage(CareerDescription $careerDescription, Image $image): void
    {
        $images = [$careerDescription->getImage()];

        foreach ($images as $img) {
            $img->setIsMain(false);
        }

        $image->setIsMain(true);
    }
}
