<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Slider;
use App\Helper\ValidatorHelper;
use App\Repository\ImageRepository;
use App\Repository\SliderRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class SliderHandler
{
    /**
     * @var SliderRepository
     */
    private $sliderRepository;

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
     * SliderHandler constructor.
     *
     * @param SliderRepository      $sliderRepository
     * @param ValidatorHelper       $validator
     * @param ImageService          $imageService
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        SliderRepository $sliderRepository,
        ValidatorHelper $validator,
        ImageService $imageService,
        ParameterBagInterface $bag
    ) {
        $this->sliderRepository = $sliderRepository;
        $this->validator = $validator;
        $this->imageService = $imageService;
        $this->bag = $bag;
    }

    /**
     * @param Slider $slider
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Slider $slider): void
    {
        $errors = $this->validator->validate($slider, null, "SetSlider");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $slider->getId()) {
            $this->sliderRepository->persist($slider);
        }

        $this->sliderRepository->flush();
    }

    /**
     * @param ParameterBag $bag
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveRowsPositions(ParameterBag $bag)
    {
        $rows = json_decode($bag->get('rows'), true);

        foreach ($rows as $row) {
            $slider = $this->sliderRepository->find($row['id']);

            $slider->setPosition($row['position']);
        }

        $this->sliderRepository->flush();
    }

    /**
     * @param Slider $slider
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Slider $slider): void
    {
        $rootDir = $this->bag->get('upload_dir');
        $imageDir = $this->bag->get('upload_image_dir');

        $image = $slider->getImage();
        $image->setFile($this->imageService->setFileObject(['file' => $rootDir.$imageDir.$image->getOriginalName(), 'fileName' => $image->getOriginalName()]));
        $image->setIsDeleted(true);

        $this->sliderRepository->delete($slider);

        $this->reorderSliders($slider->getPosition());

        $this->sliderRepository->flush();
    }

    /**
     * @param int $fromPosition
     *
     * @return void
     */
    private function reorderSliders(int $fromPosition): void
    {
        $sliders = $this->sliderRepository->getHigherThenPosition($fromPosition);

        /** @var Slider $slider */
        foreach ($sliders as $slider) {
            $slider->setPosition($fromPosition++);
        }
    }
}