<?php

declare(strict_types=1);

namespace App\Collector;

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

    /**
     * @return array<int, Promotion[]>
     */
    public function collectProductPromotions(): array
    {
        $productPromotions = $this->promotionRepository->getActivePromotionsByType(Promotion::TYPE_PRODUCT);

        $promotions = [];

        foreach ($productPromotions as $productPromotion) {
            $countOptions = $productPromotion->getPromotionOptions()->count();

            $optionTypes = $productPromotion->getOptionTypes();

            switch (true) {
                case in_array(PromotionOption::OPTION_PRODUCTS, $optionTypes):
                    $countOptions += 100;
                    break;
                case in_array(PromotionOption::OPTION_TAGS, $optionTypes):
                    $countOptions += 50;
                    break;
                case in_array(PromotionOption::OPTION_CATEGORIES, $optionTypes):
                    $countOptions += 10;
            }

            $promotions[$countOptions][] = $productPromotion;
        }

        krsort($promotions);

        return $promotions;
    }
}
