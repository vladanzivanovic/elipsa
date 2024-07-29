<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Checker\PromotionFreeShippingChecker;
use App\Collector\PromotionCollector;
use App\Entity\ShopOrder;
use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class OrderShippingPriceEventListener
{
    public function __construct(
        private readonly SettingsRepository $settingsRepository,
        private readonly PromotionCollector $promotionCollector,
        private readonly PromotionFreeShippingChecker $promotionFreeShippingChecker,
    ){}

    #[PrePersist]
    #[PreUpdate]
    public function setShippingPrice(ShopOrder $order, LifecycleEventArgs $eventArgs): void
    {
        $freeShippingSlug = ShopOrder::SHIPPING_TYPE_IN_STORE === $order->getShippingType() ? 'FREE_SHIPPING_STORE' : 'FREE_SHIPPING';

        $freeShippingPriceConfig = $this->settingsRepository->findOneBy(['slug' => $freeShippingSlug, 'country' => $order->getCountry()]);

        $shippingPriceConfig = $this->settingsRepository->findOneBy(['slug' => 'SHIPPING_PRICE', 'country' => $order->getCountry()]);

        $shippingPrice = (int) $shippingPriceConfig->getValue();

        if ($order->getTotal() >= $freeShippingPriceConfig->getValue() || $this->isFreeShippingPromotionEligible($order)) {
            $shippingPrice = 0;
        }

        $order->setShippingPrice($shippingPrice);
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
