<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;
use App\Repository\SettingsRepository;

final class OrderView
{
    private OrderProductView $orderProductView;

    private PromotionCouponView $promotionCouponView;

    private PriceView $priceView;

    private SettingsRepository $settingsRepository;

    private AddressView $addressView;

    private OrderPaymentView $orderPaymentView;

    public function __construct(
        OrderProductView $orderProductView,
        PromotionCouponView $promotionCouponView,
        PriceView $priceView,
        SettingsRepository $settingsRepository,
        AddressView $addressView,
        OrderPaymentView $orderPaymentView
    ) {
        $this->orderProductView = $orderProductView;
        $this->promotionCouponView = $promotionCouponView;
        $this->priceView = $priceView;
        $this->settingsRepository = $settingsRepository;
        $this->addressView = $addressView;
        $this->orderPaymentView = $orderPaymentView;
    }
    public function view(ShopOrder $order, string $locale): array
    {
        $total = 0;
        $freeShippingPrice = $this->settingsRepository->findOneBy(['slug' => 'FREE_SHIPPING']);
        $shippingPrice = $this->settingsRepository->findOneBy(['slug' => 'SHIPPING_PRICE']);

        $view = [
            'id' => $order->getId(),
            'token' => $order->getToken(),
            'status' => $order->getStatus(),
            'products' => [],
            'promotion' => [],
            'total' => $total,
            'total_with_shipping' => $total,
            'shipping_price' => $this->priceView->view((int) $shippingPrice->getValue(), $locale),
            'payment' => null,
            'note' => $order->getNote(),
        ];

        if (false === $order->getOrderProducts()->isEmpty()) {
            foreach ($order->getOrderProducts() as $orderProduct) {
                $productView = $this->orderProductView->view($orderProduct, $locale);

                $view['products'][] = $productView;

                if (true === $productView['is_sold']) {
                    continue;
                }

                $total += $productView['total']['unformatted_amount'];
            }
        }

        if (null !== $order->getCoupon()) {
            $view['promotion'] = $this->promotionCouponView->view($order->getCoupon());
        }

        $view['total'] = $this->priceView->view($total, $locale);

        $totalWithShipping = $total >= $freeShippingPrice->getValue() ? $total : $total + $shippingPrice->getValue();

        $view['total_with_shipping'] = $this->priceView->view($totalWithShipping, $locale);

        if (ShopOrder::STATUS_NEW < $order->getStatus()) {
            $view += $this->addCheckoutInformation($order);
        }

        if (ShopOrder::PAYMENT_TYPE_CREDIT_CARD === $order->getPaymentType()) {
            $view['payment'] = $this->orderPaymentView->view($order);
        }

        return $view;
    }

    private function addCheckoutInformation(ShopOrder $order): array
    {
        return [
            'payment_type' => $order->getPaymentType(),
            'shipping_address' => $this->addressView->view($order->getShippingAddress()),
            'billing_address' => $this->addressView->view($order->getBillingAddress()),
        ];
    }
}
