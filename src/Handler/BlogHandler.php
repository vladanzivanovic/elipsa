<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Banner;
use App\Entity\Blog;
use App\Entity\BlogHasImages;
use App\Entity\Slider;
use App\Helper\ValidatorHelper;
use App\Repository\BannerRepository;
use App\Repository\BlogRepository;
use App\Repository\ImageRepository;
use App\Repository\SliderRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class BlogHandler
{
    private \App\Helper\ValidatorHelper $validator;

    private \App\Services\ImageService $imageService;

    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $bag;

    private \App\Repository\BlogRepository $blogRepository;

    public function __construct(
        BlogRepository $blogRepository,
        ValidatorHelper $validator,
        ImageService $imageService,
        ParameterBagInterface $bag
    ) {
        $this->validator = $validator;
        $this->imageService = $imageService;
        $this->bag = $bag;
        $this->blogRepository = $blogRepository;
    }

    /**
     *
     *
     * @throws \Exception
     */
    public function save(Blog $blog): void
    {
        $errors = $this->validator->validate($blog, null, "SetBlog");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $blog->getId()) {
            $this->blogRepository->persist($blog);
        }

        $this->blogRepository->flush();
    }

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Blog $blog): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        $image = $blog->getImage();

        $image->setFile($this->imageService->setFileObject(['file' => $rootDir.$imageDir.$image->getOriginalName(), 'fileName' => $image->getOriginalName()]));
        $image->setIsDeleted(true);

        $this->blogRepository->delete($blog);

        $this->blogRepository->flush();
    }
}