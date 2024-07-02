<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Promotion;
use App\Entity\PromotionOption;
use App\Request\Dto\Admin\PromotionCouponRequestDto;
use App\Request\Dto\Admin\PromotionOptionRequestDto;

final class CouponsEditRequestParser
{
    public function parse(
        PromotionCouponRequestDto $promotionCouponRequestDto,
        Promotion $promotion = null
    ): Promotion {
        if (!$promotion instanceof Promotion) {
            $promotion = new Promotion();
        }

        $promotion->setCode($promotionCouponRequestDto->code);
        $promotion->setValidFrom($promotionCouponRequestDto->validFrom);
        $promotion->setValidTo($promotionCouponRequestDto->validTo);
        $promotion->setDiscount($promotionCouponRequestDto->discount);
        $promotion->setType($promotionCouponRequestDto->type);
        $promotion->setAvailableCountries($promotionCouponRequestDto->availableCountries);

        $this->parseOptionData($promotionCouponRequestDto->options, $promotion);

        return $promotion;
    }

    private function parseOptionData(
        ?PromotionOptionRequestDto $promotionOptionRequestDto,
        Promotion $promotion
    ): void {
        $promotion->getPromotionOptions()->clear();

        foreach ($promotionOptionRequestDto->toArray() as $type => $values) {
            if (null === $values) {
                continue;
            }

            $option = new PromotionOption();
            $option->setType($type);
            $option->setConfiguration(is_array($values) ? $values : [$values]);

            $promotion->addPromotionOption($option);
        }
    }
}
