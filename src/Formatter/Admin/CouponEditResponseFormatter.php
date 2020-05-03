<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Banner;
use App\Entity\PromotionCoupon;
use Symfony\Component\Routing\RouterInterface;

final class CouponEditResponseFormatter
{
    /**
     * @param PromotionCoupon $coupon
     *
     * @return array
     */
    public function formatResponse(PromotionCoupon $coupon): array
    {
        return [
            'code' => $coupon->getCode(),
            'valid_from' => $coupon->getValidFrom()->format('d.m.Y'),
            'valid_to' => $coupon->getValidTo()->format('d.m.Y'),
            'discount' => $coupon->getDiscount(),
        ];
    }
}