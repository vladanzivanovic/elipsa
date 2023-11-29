<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Banner;
use App\Entity\BannerTranslation;
use App\Entity\PromotionCoupon;
use App\Entity\PromotionOption;
use App\Repository\BannerRepository;
use App\Repository\PromotionCouponRepository;
use App\Request\Dto\PromotionCouponRequestDto;
use App\Request\Dto\PromotionOptionRequestDto;
use App\Services\BannerImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class CouponsEditRequestParser
{
    public function parse(PromotionCouponRequestDto $promotionCouponRequestDto, PromotionCoupon $coupon = null): PromotionCoupon
    {
        if (!$coupon instanceof PromotionCoupon) {
            $coupon = new PromotionCoupon();
        }

        $coupon->setCode($promotionCouponRequestDto->code);
        $coupon->setValidFrom($promotionCouponRequestDto->validFrom);
        $coupon->setValidTo($promotionCouponRequestDto->validTo);
        $coupon->setDiscount($promotionCouponRequestDto->discount);

        $this->parseOptionData($promotionCouponRequestDto->options, $coupon);

        return $coupon;
    }

    private function parseOptionData(
        ?PromotionOptionRequestDto $promotionOptionRequestDto,
        PromotionCoupon $promotionCoupon
    ): void {
        $promotionCoupon->getPromotionOptions()->clear();

        foreach ($promotionOptionRequestDto->toArray() as $type => $values) {
            if (null === $values) {
                continue;
            }

            $option = new PromotionOption();
            $option->setType($type);
            $option->setConfiguration(!is_array($values) ? [$values] : $values);

            $promotionCoupon->addPromotionOption($option);
        }
    }
}
