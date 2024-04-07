<?php

declare(strict_types=1);

namespace App\Parser;

use App\Checker\PromotionCheckerInterface;
use App\Checker\PromotionCheckerTrait;
use App\Checker\PromotionOptionCheckerInterface;
use App\Checker\PromotionProductChecker;
use App\Collector\PromotionCollector;
use App\Entity\Product;
use App\Entity\Promotion;

final class ProductPromotionParser
{
    private PromotionCollector $promotionCollector;

    private PromotionProductChecker $promotionProductChecker;

    public function __construct(
        PromotionCollector $promotionCollector,
        PromotionProductChecker $promotionProductChecker
    ){
        $this->promotionCollector = $promotionCollector;
        $this->promotionProductChecker = $promotionProductChecker;
    }

    /**
     * @param array|null $productPromotions - in ProductFormatter set the prop explicitly, so don't need to collect promotions every time in foreach loop
     */
    public function setProductPromotion(Product $product, array $productPromotions = null): ?Promotion
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
            $price = $product->getDiscount() > 0 ? $product->getDiscount() : $product->getPrice();

            $discountAmount = $price * ((100 - $eligiblePromotion->getDiscount()) / 100);

            $product->setPromoDiscount((int)$discountAmount);

            return $eligiblePromotion;
        }

        return null;
    }
}
