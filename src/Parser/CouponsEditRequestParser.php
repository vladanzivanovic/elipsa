<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Banner;
use App\Entity\BannerTranslation;
use App\Entity\Promotion;
use App\Entity\PromotionOption;
use App\Repository\BannerRepository;
use App\Repository\PromotionRepository;
use App\Request\Dto\PromotionCouponRequestDto;
use App\Request\Dto\PromotionOptionRequestDto;
use App\Services\BannerImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class CouponsEditRequestParser
{
    public function parse(PromotionCouponRequestDto $promotionCouponRequestDto, Promotion $promotion = null): Promotion
    {
        if (!$promotion instanceof Promotion) {
            $promotion = new Promotion();
        }

        $promotion->setCode($promotionCouponRequestDto->code);
        $promotion->setValidFrom($promotionCouponRequestDto->validFrom);
        $promotion->setValidTo($promotionCouponRequestDto->validTo);
        $promotion->setDiscount($promotionCouponRequestDto->discount);
        $promotion->setType($promotionCouponRequestDto->type);

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
