<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\Promotion;

trait PromotionCheckerTrait
{
    private function checkCouponIsEligible(Promotion $promotionCoupon)
    {
        $checkerTypes = [Promotion::CHECKER_TYPE_VALIDITY];

        foreach ($this->promotionCheckers as $promotionChecker) {
            if (true === in_array($promotionChecker->getType(), $checkerTypes)) {
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

        $isOptionApplicable = false;

        foreach ($this->promotionCheckers as $promotionChecker) {
            if (true === in_array($promotionChecker->getType(), $checkerTypes)) {
                $isOptionApplicable = $promotionChecker->isEligible($orderProduct, $promotionCoupon->getOptionByType($promotionChecker->getType()));

                if (false === $isOptionApplicable) {
                    return false;
                }
            }
        }

        return $isOptionApplicable;
    }

    private function checkCouponOptionsAreEligibleForProduct(Product $product, Promotion $promotionCoupon): bool
    {
        $checkerTypes = $promotionCoupon->getOptionTypes();

        if (null === $checkerTypes) {
            return true;
        }

        $isOptionApplicable = false;

        foreach ($this->promotionCheckers as $promotionChecker) {
            if (true === in_array($promotionChecker->getType(), $checkerTypes)) {
                $isOptionApplicable = $promotionChecker->isProductEligible($product, $promotionCoupon->getOptionByType($promotionChecker->getType()));

                if (false === $isOptionApplicable) {
                    return false;
                }
            }
        }

        return $isOptionApplicable;
    }
}
