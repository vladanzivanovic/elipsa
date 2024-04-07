<?php

declare(strict_types=1);

namespace App\Parser\Site;

use App\Entity\OrderProduct;
use App\Entity\OrderProductTranslation;
use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Entity\ShopOrder;
use App\Repository\ImageRepository;
use App\Repository\OrderProductRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ShopOrderRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class CartEditRequestParser
{
    private \App\Repository\OrderProductRepository $productRepository;

    private \App\Repository\ShopOrderRepository $orderRepository;

    public function __construct(
        OrderProductRepository $orderProductRepository,
        ShopOrderRepository $orderRepository
    ) {
        $this->productRepository = $orderProductRepository;
        $this->orderRepository = $orderRepository;
    }

    
    public function parse(Request $request): ShopOrder
    {
        $session = $request->getSession();
        $order = $this->orderRepository->getByToken($session->get('order'));

        foreach ($request->request->all() as $id => $quantity) {
            $product = $this->productRepository->find($id);
            $product->setQuantity((int) $quantity);
        }

        return $order;
    }
}