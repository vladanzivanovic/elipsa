<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\PromotionEligibilityInterface;
use App\Entity\PromotionOption;

final class PromotionOptionDiscountChecker
{
    public function isEligible(PromotionEligibilityInterface $promotionEligibility, PromotionOption $promotionOption): bool
    {
        $applicableOnDiscountedProducts = $promotionOption->getConfiguration()[0];

        return ($promotionEligibility->getDiscount() === 0) || ($promotionEligibility->getDiscount() > 0 && true === $applicableOnDiscountedProducts);
    }

    public function isProductEligible(Product $product, PromotionOption $promotionOption): bool
    {
        $applicableOnDiscountedProducts = $promotionOption->getConfiguration()[0];

        return ($product->getDiscount() === 0) || ($product->getDiscount() > 0 && true === $applicableOnDiscountedProducts);
    }

    public function getType(): string
    {
        return PromotionOption::OPTION_ALL_PRODUCTS;
    }

    public static function getDefaultPriority(): int
    {
        return 900;
    }
}
