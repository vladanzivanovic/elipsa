<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\PromotionOption;

final class PromotionOptionTagChecker implements PromotionOptionCheckerInterface
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
        return PromotionOption::OPTION_TAGS;
    }

    private function check(Product $product, PromotionOption $promotionOption): bool
    {
        $promotionTags = $promotionOption->getConfiguration();

        $hasTags = $product->getProductHasTags();

        foreach ($promotionTags as $promotionTag) {
            foreach ($hasTags as $hasTag) {
                if ((int) $promotionTag === $hasTag->getTag()->getId()) {
                    return true;
                }
            }
        }

        return false;
    }
}
