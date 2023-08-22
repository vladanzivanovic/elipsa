<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ProductTranslation;
use App\Entity\ShopOrder;
use App\Exception\ProductManipulationException;
use App\Repository\ImageRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductTranslationRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class OrderProductRequestParser
{
    private ProductColorRepository $colorRepository;

    private ImageRepository $imageRepository;

    private ProductTranslationRepository $productTranslationRepository;

    private OrderProductTranslationParser $orderProductTranslationParser;

    private OrderCouponParser $orderCouponParser;

    private OrderRequestParser $orderRequestParser;

    public function __construct(
        ProductColorRepository $colorRepository,
        ImageRepository $imageRepository,
        ProductTranslationRepository $productTranslationRepository,
        OrderProductTranslationParser $orderProductTranslationParser,
        OrderCouponParser $orderCouponParser,
        OrderRequestParser $orderRequestParser
    ) {
        $this->colorRepository = $colorRepository;
        $this->imageRepository = $imageRepository;
        $this->productTranslationRepository = $productTranslationRepository;
        $this->orderProductTranslationParser = $orderProductTranslationParser;
        $this->orderCouponParser = $orderCouponParser;
        $this->orderRequestParser = $orderRequestParser;
    }
    public function parse(
        Request $request,
        string $orderToken,
        string $productTranslationSlug
    ): ShopOrder {
        $order = $this->getOrder($orderToken);

        $body = json_decode($request->getContent(), true);
        $requestBag = new ParameterBag($body);
        $size = $requestBag->get('size');
        $color = $this->colorRepository->find($requestBag->getInt('color'));

        $orderProduct = $this->findOrCreate(
            $order,
            $productTranslationSlug,
            $size,
            $color
        );

        $product = $orderProduct->getProduct();

        Assert::notFalse($product->hasColor($color));
        Assert::notFalse($product->isSizeAvailable($size));

        $orderProduct->setColor($color);
        $orderProduct->setSize($size);
        $orderProduct->setQuantity($requestBag->getInt('quantity'));
        $orderProduct->setImage($this->imageRepository->getMainByProduct($product));
        $orderProduct->setPrice($product->getPrice());
        $orderProduct->setCode($product->getCode());
        $orderProduct->setDiscount($product->getDiscount());

        if (null !== $order->getCoupon()) {
            $this->orderCouponParser->setPromotionPriceOnOrderItems($order->getCoupon(), $orderProduct);
        }

        $this->setTranslations($product, $orderProduct);

        $order->addOrderProduct($orderProduct);

        return $order;
    }

    public function getOrder(string $orderToken): ShopOrder
    {
        return $this->orderRequestParser->findOrder($orderToken);
    }

    private function findOrCreate(
        ShopOrder $order,
        string $productTranslationSlug,
        string $size,
        ProductColor $color
    ): OrderProduct {
        $orderProduct = $order->getOrderProductByValues(
            $productTranslationSlug,
            $size,
            $color
        );

        if (null === $orderProduct) {
            $productTranslation = $this->productTranslationRepository->findOneBy(['slug' => $productTranslationSlug]);

            if (true === $productTranslation->getProduct()->isSold()) {
                throw new ProductManipulationException('product.is_sold');
            }

            $orderProduct = new OrderProduct();

            $orderProduct->setProduct($productTranslation->getProduct());
            $orderProduct->setOrderId($order);
        }

        return $orderProduct;
    }

    private function setTranslations(Product $product, OrderProduct $orderProduct): void
    {
        foreach ($product->getProductTranslations() as $productTranslation) {
            $orderProductTranslation = $this->orderProductTranslationParser->parse($productTranslation, $orderProduct);

            $orderProduct->addOrderProductTranslation($orderProductTranslation);
        }
    }
}
