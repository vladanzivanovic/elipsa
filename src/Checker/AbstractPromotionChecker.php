<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Promotion;
use App\Entity\Resources\PromotionEligibilityInterface;

abstract class AbstractPromotionChecker
{
    /**
     * @var array <int, PromotionCheckerInterface>
     */
    private array $promotionCheckers;



    public function __construct(
        iterable $promotionCheckers
    ) {
        $this->promotionCheckers = iterator_to_array($promotionCheckers);
    }

    abstract public function checkEligibility(
        PromotionEligibilityInterface $promotionEligibilityEntity,
        Promotion $promotionCoupon
    );

    protected function checkCouponIsEligible(Promotion $promotionCoupon): void
    {
        $checkerTypes = [Promotion::CHECKER_TYPE_VALIDITY];

        foreach ($this->promotionCheckers as $promotionChecker) {
            if (in_array($promotionChecker->getType(), $checkerTypes)) {
                $promotionChecker->isEligible($promotionCoupon);
            }
        }
    }
}
