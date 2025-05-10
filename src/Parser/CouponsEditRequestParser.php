<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Promotion;
use App\Entity\PromotionOption;
use App\Entity\Resources\StatusInterface;
use App\Entity\Tags;
use App\Event\PromotionTagEvent;
use App\Request\Dto\Admin\PromotionCouponRequestDto;
use App\Request\Dto\Admin\PromotionOptionRequestDto;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class CouponsEditRequestParser
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher
    ) {

    }
    public function parse(
        PromotionCouponRequestDto $promotionCouponRequestDto,
        Promotion $promotion = null
    ): Promotion {
        if (!$promotion instanceof Promotion) {
            $promotion = new Promotion();
            $promotion->setStatus(StatusInterface::STATUS_ACTIVE);
        }

        $promotion->setCode($promotionCouponRequestDto->code);
        $promotion->setValidFrom($promotionCouponRequestDto->validFrom);
        $promotion->setValidTo($promotionCouponRequestDto->validTo);
        $promotion->setDiscount($promotionCouponRequestDto->discount);
        $promotion->setType($promotionCouponRequestDto->type);
        $promotion->setAvailableCountries($promotionCouponRequestDto->availableCountries);
        $promotion->setTagTranslations($promotionCouponRequestDto->translations);

        $this->parseOptionData($promotionCouponRequestDto->options, $promotion);

        $event = new PromotionTagEvent($promotion);

        $this->eventDispatcher->dispatch($event);

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
