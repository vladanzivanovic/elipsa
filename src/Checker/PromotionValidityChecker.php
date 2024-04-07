<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Promotion;
use App\Exception\CouponCheckerException;

final class PromotionValidityChecker implements PromotionCheckerInterface
{
    /**
     * @throws CouponCheckerException
     */
    public function isEligible(
        Promotion $promotionCoupon
    ): void {
        $this->throwExceptionIfExpired($promotionCoupon);
    }

    public function getType(): string
    {
        return Promotion::CHECKER_TYPE_VALIDITY;
    }

    public static function getDefaultPriority(): int
    {
        return 1000;
    }

    /**
     * @throws CouponCheckerException
     */
    private function throwExceptionIfExpired(Promotion $coupon): void
    {
        $now = new \DateTimeImmutable();

        if ($coupon->getValidTo() >= $now && $coupon->getValidFrom() <= $now) {
            return;
        }

        throw new CouponCheckerException(
            'promo_coupon.expired'
        );
    }
}
