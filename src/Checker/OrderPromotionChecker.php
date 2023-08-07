<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Exception\OrderException;

final class OrderPromotionChecker
{
    /**
     * @throws OrderException
     */
    public function checkCoupon(
        PromotionCoupon $coupon,
        ShopOrder $order
    ): void {
        $this->throwExceptionIfNotValid($coupon);
    }

    private function throwExceptionIfNotValid(PromotionCoupon $coupon): void
    {
        $now = new \DateTimeImmutable();

        if ($coupon->getValidFrom() >= $now && $coupon->getValidTo() <= $now) {
            throw new OrderException(
                'promo_coupon.expired'
            );
        }
    }
}
