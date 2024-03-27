<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ShopOrder;
use App\Exception\ProductManipulationException;
use App\Parser\ProductPromotionParser;
use App\Repository\ImageRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductTranslationRepository;
use App\Request\Dto\OrderProductRequestDto;
use Doctrine\ORM\NonUniqueResultException;
use Webmozart\Assert\Assert;

final class OrderProductRequestParser
{
    private ProductColorRepository $colorRepository;

    private ImageRepository $imageRepository;

    private ProductTranslationRepository $productTranslationRepository;

    private OrderProductTranslationParser $orderProductTranslationParser;

    private OrderCouponParser $orderCouponParser;

    private OrderRequestParser $orderRequestParser;

    private ProductPromotionParser $productPromotionParser;

    public function __construct(
        ProductColorRepository $colorRepository,
        ImageRepository $imageRepository,
        ProductTranslationRepository $productTranslationRepository,
        OrderProductTranslationParser $orderProductTranslationParser,
        OrderCouponParser $orderCouponParser,
        OrderRequestParser $orderRequestParser,
        ProductPromotionParser $productPromotionParser
    ) {
        $this->colorRepository = $colorRepository;
        $this->imageRepository = $imageRepository;
        $this->productTranslationRepository = $productTranslationRepository;
        $this->orderProductTranslationParser = $orderProductTranslationParser;
        $this->orderCouponParser = $orderCouponParser;
        $this->orderRequestParser = $orderRequestParser;
        $this->productPromotionParser = $productPromotionParser;
    }

    /**
     * @throws ProductManipulationException
     * @throws NonUniqueResultException
     */
    public function parse(
        OrderProductRequestDto $orderProductRequestDto
    ): ShopOrder {
        $order = $this->getOrder($orderProductRequestDto->token);

        $size = $orderProductRequestDto->size;
        $color = $this->colorRepository->find($orderProductRequestDto->color);

        $orderProduct = $this->findOrCreate(
            $order,
            $orderProductRequestDto->productSlug,
            $size,
            $color
        );

        $product = $orderProduct->getProduct();

        $promotion = $this->productPromotionParser->setProductPromotion($product);

        Assert::notFalse($product->hasColor($color));

        if (false === $product->isSizeAvailable($size)) {
            throw new ProductManipulationException('product.size_unavailable');
        }

        $orderProduct->setColor($color);
        $orderProduct->setSize($size);
        $orderProduct->setQuantity($orderProductRequestDto->quantity);
        $orderProduct->setImage($this->imageRepository->getMainByProduct($product));
        $orderProduct->setPrice($product->getPrice());
        $orderProduct->setCode($product->getCode());
        $orderProduct->setDiscount($product->getPromoDiscount() ?? $product->getDiscount());

        if (null !== $promotion) {
            $orderProduct->setPromotion($promotion);
        }

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

    /**
     * @throws ProductManipulationException
     */
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
