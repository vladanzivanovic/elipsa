<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\PromotionOption;

final class PromotionOptionCategoryChecker implements PromotionOptionCheckerInterface
{
    public function isEligible(OrderProduct $orderProduct, PromotionOption $promotionOption): bool
    {
        return $this->check($orderProduct->getProduct(), $promotionOption);
    }

    public function isProductEligible(Product $product, PromotionOption $promotionOption): bool
    {
        return $this->check($product, $promotionOption);
    }

    private function check(Product $product, PromotionOption $promotionOption): bool
    {
        $promotionCategories = $promotionOption->getConfiguration();

        $categories = $product->getCategories();

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

    public static function getDefaultPriority(): int
    {
        return 10;
    }
}
