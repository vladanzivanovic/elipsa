<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\PromotionOption;

final class PromotionOptionTagChecker implements PromotionOptionCheckerInterface
{
    public function isEligible(OrderProduct $orderProduct, PromotionOption $promotionOption): bool
    {
        $promotionTags = $promotionOption->getConfiguration();

        $hasTags = $orderProduct->getProduct()->getProductHasTags();

        foreach ($promotionTags as $promotionTag) {
            foreach ($hasTags as $hasTag) {
                if ((int) $promotionTag === $hasTag->getTag()->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getType(): string
    {
        return PromotionOption::OPTION_TAGS;
    }
}
