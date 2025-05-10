<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Promotion;
use Symfony\Contracts\EventDispatcher\Event;

class PromotionTagEvent extends Event
{
    public function __construct(private readonly Promotion $promotion){}

    public function getPromotion(): Promotion
    {
        return $this->promotion;
    }
}
