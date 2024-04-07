<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Product;
use App\Entity\Promotion;
use App\Entity\PromotionEligibilityInterface;
use App\Entity\PromotionOption;
use Webmozart\Assert\Assert;

final class PromotionProductChecker extends AbstractPromotionChecker
{
    /**
     * @var array <int, PromotionOptionCheckerInterface>
     */
    private array $promotionOptionCheckers;

    private PromotionOptionDiscountChecker $promotionOptionDiscountChecker;

    public function __construct(
        iterable $promotionCheckers,
        iterable $promotionOptionCheckers,
        PromotionOptionDiscountChecker $promotionOptionDiscountChecker
    ){
        $this->promotionOptionCheckers = iterator_to_array($promotionOptionCheckers);
        $this->promotionOptionDiscountChecker = $promotionOptionDiscountChecker;

        parent::__construct($promotionCheckers);
    }

    public function checkEligibility(PromotionEligibilityInterface $product, Promotion $promotionCoupon)
    {
        Assert::isInstanceOf($product, Product::class);

        if (false === $this->checkCouponIsEligible($promotionCoupon)) {
            return false;
        }

        if (
            null !== $product->getDiscount() &&
            false === $this->promotionOptionDiscountChecker->isEligible($product, $promotionCoupon->getOptionByType(PromotionOption::OPTION_ALL_PRODUCTS))
        ) {
            return false;
        }

        $checkerTypes = $promotionCoupon->getOptionTypes();

        if ([] === $checkerTypes) {
            return true;
        }

        $isOptionApplicable = false;

        foreach ($this->promotionOptionCheckers as $promotionOptionChecker) {
            if (in_array($promotionOptionChecker->getType(), $checkerTypes)) {
                $isOptionApplicable = $promotionOptionChecker->isProductEligible($product, $promotionCoupon->getOptionByType($promotionOptionChecker->getType()));

                if (true === $isOptionApplicable) {
                    return $promotionOptionChecker::getDefaultPriority();
                }
            }
        }

        return $isOptionApplicable;
    }
}
