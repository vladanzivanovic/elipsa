<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\ProductColor;
use App\Entity\ProductTranslation;
use App\Entity\ShopOrder;
use App\Repository\ImageRepository;
use App\Repository\OrderProductRepository;
use App\Repository\ProductColorRepository;
use Symfony\Component\Routing\RouterInterface;

final class OrderEditResponseFormatter
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var ProductColorRepository
     */
    private $colorRepository;
    /**
     * @var OrderProductRepository
     */
    private $orderProductRepository;

    /**
     * OrderEditResponseFormatter constructor.
     *
     * @param RouterInterface        $router
     * @param ImageRepository        $imageRepository
     * @param ProductColorRepository $colorRepository
     * @param OrderProductRepository $orderProductRepository
     */
    public function __construct(
        RouterInterface $router,
        ImageRepository $imageRepository,
        ProductColorRepository $colorRepository,
        OrderProductRepository $orderProductRepository
    ) {
        $this->router = $router;
        $this->imageRepository = $imageRepository;
        $this->colorRepository = $colorRepository;
        $this->orderProductRepository = $orderProductRepository;
    }

    /**
     * @param ProductTranslation $productTranslation
     * @param ShopOrder          $order
     * @param string             $size
     * @param int                $color
     * @param int                $quantity
     *
     * @return array
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function formatResponse(ProductTranslation $productTranslation, ShopOrder $order, string $size, int $color, int $quantity): array
    {
        $colorEntity = $this->colorRepository->find($color);
        $image = $this->imageRepository->getFirstByColorAndProduct($productTranslation->getProduct(), $colorEntity);
        $product = $productTranslation->getProduct();

        $orderProductId = $this->orderProductRepository->getByArguments($order, $product, $size, $colorEntity);

        return [
            'product_id'        => $orderProductId,
            'product_name'      => $productTranslation->getTitle(),
            'product_slug'      => $productTranslation->getSlug(),
            'image_link'        => $this->router->generate('app.image_show', ['name' => $image->getName(), 'filter' => 'cart_thumb']),
            'product_price'     => $product->getDiscount() > 0 ? $product->getDiscount() : $product->getPrice(),
            'product_quantity'  => $quantity,
        ];
    }
}