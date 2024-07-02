<?php

declare(strict_types=1);

namespace App\View;

use App\Checker\PromotionCheckerTrait;
use App\Checker\PromotionFreeShippingChecker;
use App\Collector\PromotionCollector;
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

    public function view(ShopOrder $order, int $total, string $locale): array
    {
        $view = [
            'type' => $order->getShippingType(),
            'human_type' => $this->translator->trans('order.shipping.'.$order->getShippingType()),
            'price' => $this->setShippingPrice($order, $total, $locale),
        ];

        $view['store'] = $this->setStoreLocation($order);

        return $view;
    }

    private function setStoreLocation(ShopOrder $order): ?array
    {
        if ($order->getStoreId() instanceof \App\Entity\Location) {
            return $this->locationView->view($order->getStoreId());
        }

        return null;
    }

    private function setShippingPrice(ShopOrder $order, int $total, string $locale): array
    {
        $countryCode = $this->requestStack->getCurrentRequest()->attributes->get('_country');

        $freeShippingPriceConfig = $this->settingsRepository->findOneBy(['slug' => 'FREE_SHIPPING', 'country' => $countryCode]);
        $shippingPriceConfig = $this->settingsRepository->findOneBy(['slug' => 'SHIPPING_PRICE', 'country' => $countryCode]);

        $shippingPrice = (int) $shippingPriceConfig->getValue();

        if ($total >= $freeShippingPriceConfig->getValue() || $this->isFreeShippingPromotionEligible($order)) {
            $shippingPrice = 0;
        }

        return $this->priceView->view($shippingPrice, $locale);
    }

    private function isFreeShippingPromotionEligible(ShopOrder $order): bool
    {
        $promotions = $this->promotionCollector->collectFreeShippingPromotions();

        foreach ($promotions as $promotionElements) {
            $isEligible = $this->promotionFreeShippingChecker->checkEligibility($order, $promotionElements);

            if (true === $isEligible) {
                return true;
            }
        }

        return false;
    }
}
