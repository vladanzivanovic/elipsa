<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\ShopOrder;
use App\Repository\SettingsRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderShippingView
{
    private PriceView $priceView;

    private SettingsRepository $settingsRepository;

    private TranslatorInterface $translator;

    private LocationView $locationView;

    public function __construct(
        PriceView $priceView,
        SettingsRepository $settingsRepository,
        TranslatorInterface $translator,
        LocationView $locationView
    ) {
        $this->priceView = $priceView;
        $this->settingsRepository = $settingsRepository;
        $this->translator = $translator;
        $this->locationView = $locationView;
    }

    public function view(ShopOrder $order, int $total, string $locale): array
    {
        $view = [
            'type' => $order->getShippingType(),
            'human_type' => $this->translator->trans('order.shipping.'.$order->getShippingType()),
            'price' => $this->setShippingPrice($total, $locale),
        ];

        $view['store'] = $this->setStoreLocation($order);

        return $view;
    }

    private function setStoreLocation(ShopOrder $order): ?array
    {
        if (null !== $order->getStoreId()) {
            return $this->locationView->view($order->getStoreId());
        }

        return null;
    }

    private function setShippingPrice(int $total, string $locale): array
    {
        $freeShippingPriceConfig = $this->settingsRepository->findOneBy(['slug' => 'FREE_SHIPPING']);
        $shippingPriceConfig = $this->settingsRepository->findOneBy(['slug' => 'SHIPPING_PRICE']);

        $shippingPrice = (int) $shippingPriceConfig->getValue();

        if ($total >= $freeShippingPriceConfig->getValue()) {
            $totalWithShipping = $total;
            $shippingPrice = 0;
        }

        return $this->priceView->view($shippingPrice, $locale);
    }
}
