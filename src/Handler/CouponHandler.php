<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Banner;
use App\Entity\Promotion;
use App\Entity\Slider;
use App\Helper\ValidatorHelper;
use App\Repository\BannerRepository;
use App\Repository\ImageRepository;
use App\Repository\PromotionRepository;
use App\Repository\SliderRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class CouponHandler
{
    /**
     * @var ValidatorHelper
     */
    private $validator;

    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @var PromotionRepository
     */
    private $couponRepository;

    /**
     * @param PromotionRepository $couponRepository
     * @param ValidatorHelper           $validator
     * @param ParameterBagInterface     $bag
     */
    public function __construct(
        PromotionRepository $couponRepository,
        ValidatorHelper $validator,
        ParameterBagInterface $bag
    ) {
        $this->validator = $validator;
        $this->bag = $bag;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @param Promotion $promotion
     *
     * @return void
     *
     * @throws \Exception
     */
    public function save(Promotion $promotion): void
    {
        $errors = $this->validator->validate($promotion, null, "SetCoupon");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $promotion->getId()) {
            $this->couponRepository->persist($promotion);
        }

        $this->couponRepository->flush();
    }

    /**
     * @param Promotion $promotion
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Promotion $promotion): void
    {
        $this->couponRepository->delete($promotion);

        $this->couponRepository->flush();
    }
}
