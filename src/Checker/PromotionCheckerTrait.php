<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\Promotion;
use App\Entity\PromotionOption;

trait PromotionCheckerTrait
{
    private function checkCouponIsEligible(Promotion $promotionCoupon): void
    {
        $checkerTypes = [Promotion::CHECKER_TYPE_VALIDITY];

        foreach ($this->promotionCheckers as $promotionChecker) {
            if (in_array($promotionChecker->getType(), $checkerTypes)) {
                $promotionChecker->isEligible($promotionCoupon);
            }
        }
    }

    private function checkCouponOptionsAreEligibleForOrderProduct(OrderProduct $orderProduct, Promotion $promotionCoupon): bool
    {
        $checkerTypes = $promotionCoupon->getOptionTypes();

        if (null === $checkerTypes) {
            return true;
        }

        foreach ($this->promotionCheckers as $promotionChecker) {
            if (
                $promotionChecker->getType() === PromotionOption::OPTION_ALL_PRODUCTS &&
                false === $promotionChecker->isEligible($orderProduct, $promotionCoupon->getOptionByType(PromotionOption::OPTION_ALL_PRODUCTS))
            ) {
                return false;
            }

            if (in_array($promotionChecker->getType(), $checkerTypes)) {
                $isOptionApplicable = $promotionChecker->isEligible($orderProduct, $promotionCoupon->getOptionByType($promotionChecker->getType()));

                if (true === $isOptionApplicable) {
                    return true;
                }
            }
        }

        return false;
    }
}
