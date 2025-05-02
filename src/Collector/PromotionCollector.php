<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Product;
use App\Entity\Promotion;
use App\Entity\PromotionOption;
use App\Repository\PromotionRepository;

final class PromotionCollector
{
    private PromotionRepository $promotionRepository;

    public function __construct(
        PromotionRepository $promotionRepository
    ){
        $this->promotionRepository = $promotionRepository;
    }

    public function collectFreeShippingPromotions(): array
    {
        return $this->promotionRepository->getActivePromotionsByType(Promotion::TYPE_FREE_SHIPPING);
    }

    /**
     * @return array<int, Promotion[]>
     */
    public function collectProductPromotions(): array
    {
        $productPromotions = $this->promotionRepository->getActivePromotionsByType(Promotion::TYPE_PRODUCT);

        $promotions = [];

        foreach ($productPromotions as $productPromotion) {
            $priority = 0;

            $optionTypes = $productPromotion->getOptionTypes();

            switch (true) {
                case in_array(PromotionOption::RULE_PRODUCTS, $optionTypes):
                    $priority = 100;
                    break;
                case in_array(PromotionOption::RULE_TAGS, $optionTypes):
                    $priority += 50;
                    break;
                case in_array(PromotionOption::RULE_CATEGORIES, $optionTypes):
                    $priority += 10;
            }

            $promotions[$priority][] = $productPromotion;
        }

        krsort($promotions);

        return $promotions;
    }
}
