<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\PromotionOption;

final class PromotionOptionProductChecker implements PromotionOptionCheckerInterface
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
        return PromotionOption::OPTION_PRODUCTS;
    }

    private function check(Product $product, PromotionOption $promotionOption): bool
    {
        $promotionProducts = $promotionOption->getConfiguration();

        foreach ($promotionProducts as $promotionProduct) {
            if ((int) $promotionProduct === $product->getId()) {
                return true;
            }
        }

        return false;
    }
}
