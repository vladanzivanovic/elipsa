<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Product;
use App\Entity\PromotionOption;
use App\Entity\Resources\PromotionEligibilityInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class PromotionOptionDiscountChecker
{
    public function __construct(
        private readonly RequestStack $requestStack
    ) {}

    public function isEligible(PromotionEligibilityInterface $promotionEligibility, PromotionOption $promotionOption): bool
    {
        return $this->checkEligibility($promotionEligibility, $promotionOption);
    }

    public function getType(): string
    {
        return PromotionOption::OPTION_ALL_PRODUCTS;
    }

    public static function getDefaultPriority(): int
    {
        return 900;
    }

    private function checkEligibility(PromotionEligibilityInterface $promotionEligibility, PromotionOption $promotionOption): bool
    {
        $applicableOnDiscountedProducts = $promotionOption->getConfiguration()[0];

        if ($promotionEligibility instanceof Product) {
            $discount = $promotionEligibility->getDiscount($this->requestStack->getCurrentRequest()->attributes->get('_country'));
        } else {
            $discount = $promotionEligibility->getDiscount();
        }

        return ($discount === 0) || ($discount > 0 && true === $applicableOnDiscountedProducts);
    }
}
