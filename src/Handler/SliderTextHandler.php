<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Slider;
use App\Entity\SliderText;
use App\Helper\ValidatorHelper;
use App\Repository\ImageRepository;
use App\Repository\SliderRepository;
use App\Repository\SliderTextRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class SliderTextHandler
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
     * @var SliderTextRepository
     */
    private $repository;

    /**
     * SliderHandler constructor.
     *
     * @param SliderRepository      $sliderRepository
     * @param ValidatorHelper       $validator
     * @param ImageService          $imageService
     * @param ParameterBagInterface $bag
     * @param SliderTextRepository  $repository
     */
    public function __construct(
        SliderRepository $sliderRepository,
        ValidatorHelper $validator,
        ImageService $imageService,
        ParameterBagInterface $bag,
        SliderTextRepository $repository
    ) {
        $this->sliderRepository = $sliderRepository;
        $this->validator = $validator;
        $this->imageService = $imageService;
        $this->bag = $bag;
        $this->repository = $repository;
    }

    /**
     * @param SliderText $sliderText
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(SliderText $sliderText): void
    {
        $errors = $this->validator->validate($sliderText, null, "SetSliderText");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $sliderText->getId()) {
            $this->repository->persist($sliderText);
        }

        $this->repository->flush();
    }

    /**
     * @param SliderText $sliderText
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(SliderText $sliderText): void
    {
        $this->repository->delete($sliderText);

        $this->repository->flush();
    }
}