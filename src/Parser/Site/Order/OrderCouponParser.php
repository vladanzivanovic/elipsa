<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Checker\OrderPromotionChecker;
use App\Entity\OrderProduct;
use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Exception\OrderException;
use App\Repository\PromotionCouponRepository;
use Webmozart\Assert\Assert;

final class OrderCouponParser
{
    private PromotionCouponRepository $promotionCouponRepository;

    private OrderPromotionChecker $orderPromotionChecker;

    private OrderRequestParser $orderRequestParser;

    public function __construct(
        PromotionCouponRepository $promotionCouponRepository,
        OrderPromotionChecker $orderPromotionChecker,
        OrderRequestParser $orderRequestParser
    ) {

        $this->promotionCouponRepository = $promotionCouponRepository;
        $this->orderPromotionChecker = $orderPromotionChecker;
        $this->orderRequestParser = $orderRequestParser;
    }

    /**
     * @throws OrderException
     */
    public function parse(string $orderToken, string $couponCode, bool $removeCoupon = false): ShopOrder
    {
        $order = $this->orderRequestParser->findOrder($orderToken);

        $coupon = $this->promotionCouponRepository->findOneBy(['code' => $couponCode]);

        if (true === $removeCoupon) {
            $this->removePromotion($order, $coupon);

            return $order;
        }

        $this->addPromotion($order, $coupon);

        return $order;
    }

    public function setPromotionPriceOnOrderItems(
        PromotionCoupon $coupon,
        OrderProduct $orderProduct
    ): void {
        $isEligibleOnDiscountedProducts = $coupon->isUseOnDiscountedProducts();

        if (0 < $orderProduct->getDiscount() && false === $isEligibleOnDiscountedProducts) {
            return;
        }

        $couponPrice = $coupon->getDiscount() / 100;
        $price = 0 < $orderProduct->getDiscount() ? $orderProduct->getDiscount() : $orderProduct->getPrice();

        $promotionPrice = $price * $couponPrice;

        $orderProduct->setPromotionPrice((int) -$promotionPrice);
    }

    /**
     * @throws OrderException
     */
    private function addPromotion(
        ShopOrder $order,
        ?PromotionCoupon $promotionCoupon
    ): void {
        if (null === $promotionCoupon) {
            throw new OrderException('promo_coupon.not_found');
        }

        $this->orderPromotionChecker->checkCoupon($promotionCoupon, $order);

        $order->setCoupon($promotionCoupon);

        foreach ($order->getOrderProducts() as $orderProduct) {
            $this->setPromotionPriceOnOrderItems($promotionCoupon, $orderProduct);
        }
    }

    /**
     * @throws OrderException
     */
    private function removePromotion(
        ShopOrder $order,
        PromotionCoupon $promotionCoupon
    ): void {
        if(null === $order->getCoupon()) {
            throw new OrderException('order.promo_coupon_not_exists');
        }

        Assert::same($order->getCoupon(), $promotionCoupon);

        $order->setCoupon(null);

        foreach ($order->getOrderProducts() as $orderProduct) {
            $orderProduct->setPromotionPrice(null);
        }
    }
}
