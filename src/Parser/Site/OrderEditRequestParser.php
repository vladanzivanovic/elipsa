<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\Product;
use App\Entity\ShopOrder;
use Symfony\Component\HttpFoundation\ParameterBag;

final class OrderEditRequestParser
{
    public function parse(ParameterBag $bag, Product $product, ShopOrder $order = null)
    {
        if (!$order instanceof ShopOrder) {
            $order = new ShopOrder();
            $order->setStatus(ShopOrder::STATUS_NEW);
        }

    }

    private function setProduct(ParameterBag $bag, Product $product, ShopOrder $order)
    {

    }
}