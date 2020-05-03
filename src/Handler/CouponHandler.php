<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Banner;
use App\Entity\PromotionCoupon;
use App\Entity\Slider;
use App\Helper\ValidatorHelper;
use App\Repository\BannerRepository;
use App\Repository\ImageRepository;
use App\Repository\PromotionCouponRepository;
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
     * @var PromotionCouponRepository
     */
    private $couponRepository;

    /**
     * @param PromotionCouponRepository $couponRepository
     * @param ValidatorHelper           $validator
     * @param ParameterBagInterface     $bag
     */
    public function __construct(
        PromotionCouponRepository $couponRepository,
        ValidatorHelper $validator,
        ParameterBagInterface $bag
    ) {
        $this->validator = $validator;
        $this->bag = $bag;
        $this->couponRepository = $couponRepository;
    }

    /**
     * @param PromotionCoupon $coupon
     *
     * @return void
     *
     * @throws \Exception
     */
    public function save(PromotionCoupon $coupon): void
    {
        $errors = $this->validator->validate($coupon, null, "SetCoupon");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null === $coupon->getId()) {
            $this->couponRepository->persist($coupon);
        }

        $this->couponRepository->flush();
    }

    /**
     * @param PromotionCoupon $coupon
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(PromotionCoupon $coupon): void
    {
        $this->couponRepository->delete($coupon);

        $this->couponRepository->flush();
    }
}