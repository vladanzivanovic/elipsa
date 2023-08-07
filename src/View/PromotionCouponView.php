<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\PromotionCoupon;

final class PromotionCouponView
{
    public function view(PromotionCoupon $coupon): array
    {
        $view = [
            'code' => $coupon->getCode(),
            'percentage' => $coupon->getDiscount(),
        ];

        return $view;
    }
}
