<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Blog;
use App\Entity\BlogHasImages;
use App\Entity\BlogTranslation;
use App\Entity\Image;
use App\Repository\BlogHasImagesRepository;
use App\Repository\ImageRepository;
use App\Repository\ProductHasImagesRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Webmozart\Assert\Assert;

final class BlogImageService
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
     * @param ImageService          $imageService
     * @param ParameterBagInterface $bag
     * @param ImageRepository       $imageRepository
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
     * @param BlogTranslation $blogTranslation
     * @param array           $data
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function setImages(BlogTranslation $blogTranslation, array $data): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $tmpDir = $this->bag->get('upload_tmp_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        $blog = $blogTranslation->getBlog();

        if (empty(array_filter($data))) {
            return;
        }

        Assert::true($this->validateMainImage($data), 'field.main_image');

        $slug = Urlizer::transliterate($blogTranslation->getTitle());
        $exceptions = [];

        foreach ($data as $index => $image) {

            if (isset($image['id'])) {
                $imageObj = $this->imageRepository->find($image['id']);

                if(isset($image['deleted']) && true === $image['deleted']) {
                    $image['file'] = $rootDir.$imageDir.$imageObj->getOriginalName();
                    $file = $this->img->setFileObject($image);
                    $imageObj->setFile($file);
                    $imageObj->setIsDeleted(true);

                    $this->imageRepository->delete($imageObj);

                    continue;
                }

                if (true === $image['isMain']) {
                    $this->updateImage($blog, $imageObj);
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

            $mediaObj->setRelatedToType(Image::RELATED_TYPE_BLOG);
            $mediaObj->setName($slug.'-'.++$index);
            $mediaObj->setIsmain($image['isMain']);
            $mediaObj->setOriginalName($newName);
            $mediaObj->setFile($file);
            $mediaObj->setDevice(Image::DEVICE_DESKTOP);

            $this->imageRepository->persist($mediaObj);

            $blog->setImage($mediaObj);
        }

        if (count($exceptions) > 0) {
            throw new BadRequestHttpException(json_encode(['images' => $exceptions]));
        }
    }

    /**
     * @param Blog  $blog
     * @param Image $image
     *
     * @return void
     */
    private function updateImage(Blog $blog, Image $image): void
    {
        $images = [$blog->getImage()];

        /** @var Image $image */
        foreach ($images as $img) {
            $img->setIsMain(false);
        }

        $image->setIsMain(true);
    }
}