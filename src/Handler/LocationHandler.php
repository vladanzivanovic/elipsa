<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Banner;
use App\Entity\Location;
use App\Entity\LocationHasImages;
use App\Entity\Slider;
use App\Helper\ValidatorHelper;
use App\Repository\BannerRepository;
use App\Repository\ImageRepository;
use App\Repository\LocationRepository;
use App\Repository\SliderRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class LocationHandler
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
     * @var LocationRepository
     */
    private $locationRepository;

    /**
     * @param LocationRepository    $locationRepository
     * @param ValidatorHelper       $validator
     * @param ImageService          $imageService
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        LocationRepository $locationRepository,
        ValidatorHelper $validator,
        ImageService $imageService,
        ParameterBagInterface $bag
    ) {
        $this->validator = $validator;
        $this->imageService = $imageService;
        $this->bag = $bag;
        $this->locationRepository = $locationRepository;
    }

    /**
     * @param Banner $banner
     *
     * @return void
     *
     * @throws \Exception
     */
    public function save(Location $location): void
    {
        $errors = $this->validator->validate($location, null, "SetLocation");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $location->getId()) {
            $this->locationRepository->persist($location);
        }

        $this->locationRepository->flush();
    }

    /**
     * @param Location $location
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Location $location): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        $hasImages = $location->getLocationHasImages();

        /** @var LocationHasImages $hasImage */
        foreach ($hasImages as $hasImage) {
            $image = $hasImage->getImage();

            $image->setFile($this->imageService->setFileObject(['file' => $rootDir.$imageDir.$image->getOriginalName(), 'fileName' => $image->getOriginalName()]));
            $image->setIsDeleted(true);
        }

        $this->locationRepository->removeWithFlush($location);
    }
}