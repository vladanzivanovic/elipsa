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
    /**
     * @var OrderProductRepository
     */
    private $productRepository;
    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;
    /**
     * @var ProductColorRepository
     */
    private $colorRepository;
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var ShopOrderRepository
     */
    private $orderRepository;

    /**
     * OrderProductManipulationRequestParser constructor.
     *
     * @param OrderProductRepository $orderProductRepository
     * @param ProductSizeRepository  $sizeRepository
     * @param ProductColorRepository $colorRepository
     * @param ImageRepository        $imageRepository
     * @param ShopOrderRepository    $orderRepository
     */
    public function __construct(
        OrderProductRepository $orderProductRepository,
        ProductSizeRepository $sizeRepository,
        ProductColorRepository $colorRepository,
        ImageRepository $imageRepository,
        ShopOrderRepository $orderRepository
    ) {
        $this->productRepository = $orderProductRepository;
        $this->sizeRepository = $sizeRepository;
        $this->colorRepository = $colorRepository;
        $this->imageRepository = $imageRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Request $request
     * @param Product $product
     *
     * @return ShopOrder
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function parse(Request $request, Product $product): ShopOrder
    {
        $session = $request->getSession();
        $order = null;

        if (true === $session->isStarted() && $session->has('order')) {
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

    /**
     * @param Product      $product
     * @param OrderProduct $orderProduct
     *
     * @throws \Exception
     */
    private function setProductTranslations(Product $product, OrderProduct $orderProduct): void
    {
        $translations = $product->getProductTranslations();

        /** @var ProductTranslation $trans */
        foreach ($translations->getIterator() as $trans) {
            $orderTrans = new OrderProductTranslation();
            $orderTrans->setTitle($trans->getTitle());
            $orderTrans->setSlug($trans->getSlug());
            $orderTrans->setLocale($trans->getLocale());
            $orderTrans->setOrderProduct($orderProduct);

            $orderProduct->addOrderProductTranslation($orderTrans);
        }
    }
}
