<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;
use App\Helper\ConstantsHelper;
use App\Repository\SettingsRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderView
{
    private OrderProductView $orderProductView;

    private PromotionCouponView $promotionCouponView;

    private PriceView $priceView;

    private AddressView $addressView;

    private OrderPaymentView $orderPaymentView;

    private TranslatorInterface $translator;

    private OrderShippingView $orderShippingView;

    public function __construct(
        OrderProductView $orderProductView,
        PromotionCouponView $promotionCouponView,
        PriceView $priceView,
        SettingsRepository $settingsRepository,
        AddressView $addressView,
        OrderPaymentView $orderPaymentView,
        TranslatorInterface $translator,
        OrderShippingView $orderShippingView
    ) {
        $this->orderProductView = $orderProductView;
        $this->promotionCouponView = $promotionCouponView;
        $this->priceView = $priceView;
        $this->addressView = $addressView;
        $this->orderPaymentView = $orderPaymentView;
        $this->translator = $translator;
        $this->orderShippingView = $orderShippingView;
    }

    public function view(ShopOrder $order, string $locale): array
    {
        $total = 0;

        $view = [
            'id' => $order->getId(),
            'token' => $order->getToken(),
            'status' => $order->getStatus(),
            'status_text' => $this->translator->trans('order.status.'. $order->getStatus(), [], 'messages', $locale),
            'products' => [],
            'promotion' => [],
            'payment' => $this->orderPaymentView->view($order),
            'note' => $order->getNote(),
            'checkout_completed_at' => $order->getCompletedAt() instanceof \DateTimeInterface ? $order->getCompletedAt()->format('d.m.Y H:i:s') : null,
            'tracking_info' => $order->getTrackingInfo(),
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

        if ($order->getCoupon() instanceof \App\Entity\Promotion) {
            $view['promotion'] = $this->promotionCouponView->view($order->getCoupon());
        }


        $view['shipping'] = $this->orderShippingView->view($order, $total, $locale);
        $view['total'] = $this->priceView->view($total, $locale);
        $view['total_with_shipping'] = $this->priceView->view(
            $total + $view['shipping']['price']['unformatted_amount'],
            $locale
        );

//        $view = $this->setShippingPriceAndUpdateTotal($view, $total, $locale);

        if (ShopOrder::STATUS_NEW !== $order->getStatus()) {
            $view += $this->addCheckoutInformation($order);
        }

        return $view;
    }

    private function addCheckoutInformation(ShopOrder $order): array
    {
        return [
            'shipping_address' => $this->addressView->view($order->getShippingAddress()),
            'billing_address' => $this->addressView->view($order->getBillingAddress()),
        ];
    }
}
