<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\PromotionOption;

final class PromotionOptionCategoryChecker implements PromotionOptionCheckerInterface
{
    public function isEligible(OrderProduct $orderProduct, PromotionOption $promotionOption): bool
    {
        $promotionCategories = $promotionOption->getConfiguration();

        $categories = $orderProduct->getProduct()->getCategories();

        foreach ($promotionCategories as $promotionCategoryId) {
            foreach ($categories as $category) {
                if ((int) $promotionCategoryId === $category->getId()) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getType(): string
    {
        return PromotionOption::OPTION_CATEGORIES;
    }
}
