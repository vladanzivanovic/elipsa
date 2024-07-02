<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Slider;
use App\Helper\ValidatorHelper;
use App\Repository\SliderRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class SliderHandler
{
    public function __construct(
        private readonly SliderRepository $sliderRepository,
        private readonly ValidatorHelper $validator,
        private readonly ImageService $imageService,
        private readonly ParameterBagInterface $bag
    ) {}

    /**
     *
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
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveRowsPositions(ParameterBag $bag): void
    {
        $rows = json_decode($bag->get('rows'), true);

        foreach ($rows as $row) {
            $slider = $this->sliderRepository->find($row['id']);

            $slider->setPosition($row['position']);
        }

        $this->sliderRepository->flush();
    }

    /**
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

    
    private function reorderSliders(int $fromPosition): void
    {
        $sliders = $this->sliderRepository->getHigherThenPosition($fromPosition);

        /** @var Slider $slider */
        foreach ($sliders as $slider) {
            $slider->setPosition($fromPosition++);
        }
    }
}
