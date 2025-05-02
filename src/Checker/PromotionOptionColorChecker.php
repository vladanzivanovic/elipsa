<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\PromotionOption;

final class PromotionOptionColorChecker implements PromotionOptionCheckerInterface
{
    public function isEligible(OrderProduct $orderProduct, PromotionOption $promotionOption): bool
    {
        return $this->check($orderProduct->getProduct(), $promotionOption);
    }

    public function isProductEligible(Product $product, PromotionOption $promotionOption): bool
    {
        return $this->check($product, $promotionOption);
    }

    public function getType(): string
    {
        return PromotionOption::RULE_COLORS;
    }

    public static function getDefaultPriority(): int
    {
        return 30;
    }

    private function check(Product $product, PromotionOption $promotionOption): bool
    {
        $promotionColors = $promotionOption->getConfiguration();

        $productColors = $product->getProductColors();

        foreach ($promotionColors as $promotionColor) {
            foreach ($productColors as $productColor) {
                if ((int) $promotionColor === $productColor->getId()) {
                    return true;
                }
            }
        }

        return false;
    }
}
