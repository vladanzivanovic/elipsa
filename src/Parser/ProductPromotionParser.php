<?php

declare(strict_types=1);

namespace App\Parser;

use App\Checker\PromotionFreeShippingChecker;
use App\Checker\PromotionProductChecker;
use App\Collector\PromotionCollector;
use App\Entity\Product;
use App\Entity\Promotion;

final class ProductPromotionParser
{
    public function __construct(
        private readonly PromotionCollector $promotionCollector,
        private readonly PromotionProductChecker $promotionProductChecker,
        private readonly PromotionFreeShippingChecker $promotionFreeShippingChecker,
    ){}

    /**
     * @param array|null $productPromotions - in ProductFormatter set the prop explicitly, so don't need to collect promotions every time in foreach loop
     */
    public function setProductPromotion(Product $product, string $countryCode, array $productPromotions = null): ?Promotion
    {
        if (null === $productPromotions) {
            $productPromotions = $this->promotionCollector->collectProductPromotions();
        }

        $promotionCandidates = [];

        foreach ($productPromotions as $productPromotionElements) {
            foreach ($productPromotionElements as $productPromotion) {
                $priority = $this->promotionProductChecker->checkEligibility($product, $productPromotion);

                if (is_int($priority)) {
                    $promotionCandidates[$priority] = $productPromotion;
                }
            }
        }

        ksort($promotionCandidates, SORT_NUMERIC);

        $eligiblePromotion = end($promotionCandidates);

        if (false !== $eligiblePromotion) {
            $price = $product->getDiscount($countryCode) > 0 ? $product->getDiscount($countryCode) : $product->getPrice($countryCode);

            $discountAmount = $price * ((100 - $eligiblePromotion->getDiscount()) / 100);

            $product->setPromoDiscount((int)$discountAmount, $countryCode);

            return $eligiblePromotion;
        }

        return null;
    }

    public function setFreeShippingPromotionEnabled(Product $product): void
    {
        $collection = $this->promotionCollector->collectFreeShippingPromotions();

        $product->setIsFreeShippingEnabled(false);

        foreach ($collection as $promotion) {
            $isEligible = $this->promotionFreeShippingChecker->checkProductEligibility($product, $promotion);

            if (true === $isEligible) {
                $product->setIsFreeShippingEnabled(true);
                return;
            }
        }
    }
}
