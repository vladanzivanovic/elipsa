<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Banner;
use App\Entity\BannerTranslation;
use App\Entity\PromotionCoupon;
use App\Repository\BannerRepository;
use App\Repository\PromotionCouponRepository;
use App\Services\BannerImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class CouponsEditRequestParser
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var BannerImageService
     */
    private $imageService;

    /**
     * @var PromotionCouponRepository
     */
    private $promotionCouponsRepository;

    /**
     * BannerEditRequestParser constructor.
     *
     * @param ParameterBagInterface     $parameterBag
     * @param PromotionCouponRepository $promotionCouponsRepository
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        PromotionCouponRepository $promotionCouponsRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->promotionCouponsRepository = $promotionCouponsRepository;
    }

    /**
     * @param ParameterBag         $bag
     * @param PromotionCoupon|null $coupon
     *
     * @return PromotionCoupon
     * @throws \Exception
     */
    public function parse(ParameterBag $bag, PromotionCoupon $coupon = null): PromotionCoupon
    {
        if (!$coupon instanceof PromotionCoupon) {
            $coupon = new PromotionCoupon();
        }

        $coupon->setCode($bag->get('code'));
        $coupon->setValidFrom(new \DateTime($bag->get('valid_from'). '00:00:00'));
        $coupon->setValidTo(new \DateTime($bag->get('valid_to'). '23:59:59'));
        $coupon->setDiscount((int) $bag->get('discount'));

        return $coupon;
    }
}