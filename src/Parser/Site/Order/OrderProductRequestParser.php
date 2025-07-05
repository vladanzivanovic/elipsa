<?php

declare(strict_types=1);

namespace App\Parser\Site\Order;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\Promotion;
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
    public function __construct(
        private readonly ProductColorRepository $colorRepository,
        private readonly ImageRepository $imageRepository,
        private readonly ProductTranslationRepository $productTranslationRepository,
        private readonly OrderProductTranslationParser $orderProductTranslationParser,
        private readonly OrderCouponParser $orderCouponParser,
        private readonly OrderRequestParser $orderRequestParser,
        private readonly ProductPromotionParser $productPromotionParser
    ) {}

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
            $color,
            $orderProductRequestDto->country,
        );

        $product = $orderProduct->getProduct();
        $productOption = $product->getOptionByCountry($orderProductRequestDto->country);

        $promotion = $this->productPromotionParser->setProductPromotion($product, $orderProductRequestDto->country);

        Assert::notFalse($product->hasColor($color));

        $orderProduct->setColor($color);
        $orderProduct->setSize($size);
        $orderProduct->setQuantity($orderProductRequestDto->quantity);
        $orderProduct->setImage($this->imageRepository->getMainByProduct($product));
        $orderProduct->setPrice($productOption->getPrice());
        $orderProduct->setCode($product->getCode());
        $orderProduct->setDiscount($productOption->getPromoDiscount() ?? $productOption->getDiscount());

        if (false === $orderProduct->isProductAvailable()) {
            throw new ProductManipulationException('product.size_unavailable');
        }

        if ($promotion instanceof Promotion) {
            $orderProduct->setPromotion($promotion);
        }

        if ($order->getCoupon() instanceof Promotion) {
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
        ProductColor $color,
        string $country,
    ): OrderProduct {
        $orderProduct = $order->getOrderProductByValues(
            $productTranslationSlug,
            $size,
            $color
        );

        if (!$orderProduct instanceof OrderProduct) {
            $productTranslation = $this->productTranslationRepository->findOneBy(['slug' => $productTranslationSlug]);

            $product = $productTranslation->getProduct();

            $productOption = $product->getOptionByCountry($country);

            if (null === $productOption) {
                throw new ProductManipulationException('product.not_available_for_country');
            }

            if (true === $productOption->isSold()) {
                throw new ProductManipulationException('product.is_sold');
            }

            $orderProduct = new OrderProduct();

            $orderProduct->setProduct($product);
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
