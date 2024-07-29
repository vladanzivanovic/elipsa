<?php

declare(strict_types=1);

namespace App\View;

use App\Checker\PromotionCheckerTrait;
use App\Checker\PromotionFreeShippingChecker;
use App\Collector\PromotionCollector;
use App\Entity\Location;
use App\Entity\ShopOrder;
use App\Repository\SettingsRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderShippingView
{
    use PromotionCheckerTrait;

    public function __construct(
        private readonly PriceView $priceView,
        private readonly SettingsRepository $settingsRepository,
        private readonly TranslatorInterface $translator,
        private readonly LocationView $locationView,
        private readonly PromotionCollector $promotionCollector,
        private readonly PromotionFreeShippingChecker $promotionFreeShippingChecker,
        private readonly RequestStack $requestStack
    ) {}

    public function view(ShopOrder $order, string $locale): array
    {
        $view = [
            'type' => $order->getShippingType(),
            'human_type' => $this->translator->trans('order.shipping.'.$order->getShippingType()),
            'price' => $this->priceView->view($order->getShippingPrice(), $locale),
        ];

        $view['store'] = $this->setStoreLocation($order);

        return $view;
    }

    private function setStoreLocation(ShopOrder $order): null|array
    {
        if ($order->getStoreId() instanceof Location) {
            return $this->locationView->view($order->getStoreId());
        }

        return null;
    }
}
