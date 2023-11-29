<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;

interface PromotionCheckerInterface
{
    public function isEligible(ShopOrder $order, PromotionCoupon $promotionCoupon): void;

    public function getType(): string;
}
