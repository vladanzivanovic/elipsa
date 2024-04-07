<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\OrderProduct;
use App\Entity\OrderProductTranslation;
use App\Entity\Product;
use App\Entity\ProductTranslation;
use App\Entity\ShopOrder;
use App\Repository\ImageRepository;
use App\Repository\OrderProductRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ShopOrderRepository;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class OrderProductManipulationRequestParser
{
    private \App\Repository\OrderProductRepository $productRepository;
    private \App\Repository\ProductColorRepository $colorRepository;
    private \App\Repository\ImageRepository $imageRepository;
    private \App\Repository\ShopOrderRepository $orderRepository;

    /**
     * OrderProductManipulationRequestParser constructor.
     */
    public function __construct(
        OrderProductRepository $orderProductRepository,
        ProductSizeRepository $sizeRepository,
        ProductColorRepository $colorRepository,
        ImageRepository $imageRepository,
        ShopOrderRepository $orderRepository
    ) {
        $this->productRepository = $orderProductRepository;
        $this->colorRepository = $colorRepository;
        $this->imageRepository = $imageRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function parse(Request $request, Product $product): ShopOrder
    {
        $session = $request->getSession();
        $order = null;

        if ($session->isStarted() && $session->has('order')) {
            $order = $this->orderRepository->getByToken($session->get('order'));
        }

        if (!$order instanceof ShopOrder) {
            $order = new ShopOrder();
            $order->setStatus(ShopOrder::STATUS_NEW);
            $order->setToken();
        }

        $this->setProduct($request->request, $product, $order);

        return $order;
    }

    private function setProduct(ParameterBag $bag, Product $product, ShopOrder $order): void
    {
        $color = $this->colorRepository->find($bag->getInt('color'));
        $orderProduct = $this->productRepository->findOneBy([
            'orderId'   => $order,
            'product'   => $product,
            'size'      => $bag->get('size'),
            'color'     => $color,
        ]);

        if (null == $orderProduct) {
            $orderProduct = new OrderProduct();
        }

        $orderProduct->setProduct($product);
        $orderProduct->setOrderId($order);
        $orderProduct->setColor($color);
        $orderProduct->setSize($bag->get('size'));
        $orderProduct->setQuantity($bag->getInt('quantity'));
        $orderProduct->setImage($this->imageRepository->getMainByProduct($product));
        $orderProduct->setPrice($product->getPrice());
        $orderProduct->setCode($product->getCode());
        $orderProduct->setDiscount($product->getDiscount());

        $order->addOrderProduct($orderProduct);
    }
}
