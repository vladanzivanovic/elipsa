<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Banner;
use App\Entity\Blog;
use App\Entity\BlogHasImages;
use App\Entity\CareerDescription;
use App\Entity\Slider;
use App\Helper\ValidatorHelper;
use App\Repository\BannerRepository;
use App\Repository\BlogRepository;
use App\Repository\CareerDescriptionRepository;
use App\Repository\ImageRepository;
use App\Repository\SliderRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class JobHandler
{
    /**
     * @var ValidatorHelper
     */
    private $validator;

    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @var BlogRepository
     */
    private $blogRepository;
    /**
     * @var CareerDescriptionRepository
     */
    private $repository;

    /**
     * @param BlogRepository              $blogRepository
     * @param ValidatorHelper             $validator
     * @param ImageService                $imageService
     * @param ParameterBagInterface       $bag
     * @param CareerDescriptionRepository $repository
     */
    public function __construct(
        BlogRepository $blogRepository,
        ValidatorHelper $validator,
        ImageService $imageService,
        ParameterBagInterface $bag,
        CareerDescriptionRepository $repository
    ) {
        $this->validator = $validator;
        $this->imageService = $imageService;
        $this->bag = $bag;
        $this->blogRepository = $blogRepository;
        $this->repository = $repository;
    }

    /**
     * @param CareerDescription $careerDescription
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(CareerDescription $careerDescription): void
    {
        $errors = $this->validator->validate($careerDescription, null, "SetJob");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $careerDescription->getId()) {
            $this->repository->persist($careerDescription);
        }

        $this->repository->flush();
    }

    /**
     * @param CareerDescription $careerDescription
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(CareerDescription $careerDescription): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        $image = $careerDescription->getImage();

        $image->setFile($this->imageService->setFileObject(['file' => $rootDir.$imageDir.$image->getOriginalName(), 'fileName' => $image->getOriginalName()]));
        $image->setIsDeleted(true);

        $this->repository->delete($careerDescription);

        $this->repository->flush();
    }
}