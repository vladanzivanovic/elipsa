<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Exception\OrderException;

final class PromotionValidityChecker implements PromotionCheckerInterface
{
    /**
     * @throws OrderException
     */
    public function isEligible(
        ShopOrder $order,
        PromotionCoupon $promotionCoupon
    ): void {
        $this->throwExceptionIfExpired($promotionCoupon);
    }

    public function getType(): string
    {
        return PromotionCoupon::TYPE_VALIDITY;
    }

    /**
     * @throws OrderException
     */
    private function throwExceptionIfExpired(PromotionCoupon $coupon): void
    {
        $now = new \DateTimeImmutable();

        if ($coupon->getValidTo() >= $now && $coupon->getValidFrom() <= $now) {
            return;
        }

        throw new OrderException(
            'promo_coupon.expired'
        );
    }
}
