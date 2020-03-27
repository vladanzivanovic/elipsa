<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Banner;
use App\Entity\Slider;
use App\Helper\ValidatorHelper;
use App\Repository\BannerRepository;
use App\Repository\ImageRepository;
use App\Repository\SliderRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class BannerHandler
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
     * @var BannerRepository
     */
    private $bannerRepository;

    /**
     * SliderHandler constructor.
     *
     * @param BannerRepository      $bannerRepository
     * @param ValidatorHelper       $validator
     * @param ImageService          $imageService
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        BannerRepository $bannerRepository,
        ValidatorHelper $validator,
        ImageService $imageService,
        ParameterBagInterface $bag
    ) {
        $this->validator = $validator;
        $this->imageService = $imageService;
        $this->bag = $bag;
        $this->bannerRepository = $bannerRepository;
    }

    /**
     * @param Banner $banner
     *
     * @return void
     *
     * @throws \Exception
     */
    public function save(Banner $banner): void
    {
        $errors = $this->validator->validate($banner, null, "SetBanner");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $banner->getId()) {
            $this->bannerRepository->persist($banner);
        }

        $this->bannerRepository->flush();
    }

    /**
     * @param Banner $banner
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Banner $banner): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        $image = $banner->getImage();
        $image->setFile($this->imageService->setFileObject(['file' => $rootDir.$imageDir.$image->getOriginalName(), 'fileName' => $image->getOriginalName()]));
        $image->setIsDeleted(true);

        $this->bannerRepository->delete($banner);

        $this->bannerRepository->flush();
    }
}