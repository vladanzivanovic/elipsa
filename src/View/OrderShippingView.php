<?php

declare(strict_types=1);

namespace App\View;

use App\Checker\PromotionCheckerTrait;
use App\Checker\PromotionFreeShippingChecker;
use App\Collector\PromotionCollector;
use App\Entity\ShopOrder;
use App\Repository\SettingsRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

final class OrderShippingView
{
    use PromotionCheckerTrait;

    private PriceView $priceView;

    private SettingsRepository $settingsRepository;

    private TranslatorInterface $translator;

    private LocationView $locationView;

    private PromotionCollector $promotionCollector;

    private PromotionFreeShippingChecker $promotionFreeShippingChecker;

    public function __construct(
        PriceView $priceView,
        SettingsRepository $settingsRepository,
        TranslatorInterface $translator,
        LocationView $locationView,
        PromotionCollector $promotionCollector,
        PromotionFreeShippingChecker $promotionFreeShippingChecker
    ) {
        $this->priceView = $priceView;
        $this->settingsRepository = $settingsRepository;
        $this->translator = $translator;
        $this->locationView = $locationView;
        $this->promotionCollector = $promotionCollector;
        $this->promotionFreeShippingChecker = $promotionFreeShippingChecker;
    }

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
        if (null !== $order->getStoreId()) {
            return $this->locationView->view($order->getStoreId());
        }

        return null;
    }

    private function setShippingPrice(ShopOrder $order, int $total, string $locale): array
    {
        $freeShippingPriceConfig = $this->settingsRepository->findOneBy(['slug' => 'FREE_SHIPPING']);
        $shippingPriceConfig = $this->settingsRepository->findOneBy(['slug' => 'SHIPPING_PRICE']);

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
