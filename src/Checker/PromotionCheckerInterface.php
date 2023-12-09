<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Promotion;
use App\Entity\ShopOrder;

interface PromotionCheckerInterface
{
    public function isEligible(Promotion $promotionCoupon): void;

    public function getType(): string;
}
