<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Product;
use App\Entity\ProductOptions;
use App\Entity\Promotion;
use App\Entity\PromotionOption;
use App\Entity\Resources\PromotionEligibilityInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class PromotionProductChecker extends AbstractPromotionChecker
{
    /**
     * @var array <int, PromotionOptionCheckerInterface>
     */
    private array $promotionOptionCheckers;

    public function __construct(
        iterable $promotionCheckers,
        iterable $promotionOptionCheckers,
        private readonly PromotionOptionDiscountChecker $promotionOptionDiscountChecker,
        private readonly RequestStack $requestStack
    ){
        $this->promotionOptionCheckers = iterator_to_array($promotionOptionCheckers);

        parent::__construct($promotionCheckers);
    }

    public function checkEligibility(PromotionEligibilityInterface $product, Promotion $promotionCoupon)
    {
        Assert::isInstanceOf($product, Product::class);

        $countryCode = $this->requestStack->getCurrentRequest()->attributes->get('_country');

        if (false === $this->checkCouponIsEligible($promotionCoupon)) {
            return false;
        }

        /** @var ProductOptions $productOption */
        $productOption = $product->getOptionsByCountry($countryCode);

        if (
            null !== $productOption->getDiscount() &&
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
