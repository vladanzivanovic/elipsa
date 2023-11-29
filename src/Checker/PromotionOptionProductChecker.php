<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\PromotionOption;

final class PromotionOptionProductChecker implements PromotionOptionCheckerInterface
{
    public function isEligible(OrderProduct $orderProduct, PromotionOption $promotionOption): bool
    {
        $promotionProducts = $promotionOption->getConfiguration();

        $product = $orderProduct->getProduct();

        foreach ($promotionProducts as $promotionProduct) {
            if ((int) $promotionProduct === $product->getId()) {
                return true;
            }
        }

        return false;
    }

    public function getType(): string
    {
        return PromotionOption::OPTION_PRODUCTS;
    }
}
