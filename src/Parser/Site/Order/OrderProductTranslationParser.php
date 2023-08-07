<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\OrderProduct;
use App\Entity\OrderProductTranslation;
use App\Entity\ProductTranslation;
use App\Repository\OrderProductTranslationRepository;

final class OrderProductTranslationParser
{
    private OrderProductTranslationRepository $orderProductTranslationRepository;

    public function __construct(
        OrderProductTranslationRepository $orderProductTranslationRepository
    ) {

        $this->orderProductTranslationRepository = $orderProductTranslationRepository;
    }
    public function parse(
        ProductTranslation $productTranslation,
        OrderProduct $orderProduct
    ): OrderProductTranslation {
        $orderProductTranslation = $this->findOrCreate($orderProduct, $productTranslation->getLocale());

        $orderProductTranslation->setTitle($productTranslation->getTitle());
        $orderProductTranslation->setSlug($productTranslation->getSlug());
        $orderProductTranslation->setLocale($productTranslation->getLocale());

        return $orderProductTranslation;
    }

    private function findOrCreate(OrderProduct $orderProduct, string $locale): OrderProductTranslation
    {
        $orderProductTranslation = $this->orderProductTranslationRepository
            ->findOneBy(['orderProduct' => $orderProduct, 'locale' => $locale]);

        return null !== $orderProductTranslation? $orderProductTranslation : new OrderProductTranslation();
    }
}
