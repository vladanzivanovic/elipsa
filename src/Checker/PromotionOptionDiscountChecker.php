<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\PromotionOption;

final class PromotionOptionDiscountChecker implements PromotionOptionCheckerInterface
{
    public function isEligible(OrderProduct $orderProduct, PromotionOption $promotionOption): bool
    {
        $applicableOnDiscountedProducts = $promotionOption->getConfiguration()[0];

        return ($orderProduct->getDiscount() === 0) || ($orderProduct->getDiscount() > 0 && true === $applicableOnDiscountedProducts);
    }

    public function getType(): string
    {
        return PromotionOption::OPTION_ALL_PRODUCTS;
    }
}
