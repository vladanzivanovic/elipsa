<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Notification;
use App\Repository\ProductRepository;
use App\Request\Dto\NotificationRequestDto;

final class ProductSizeAvailableChecker implements NotificationCheckerInterface
{
    private ProductRepository $productRepository;

    public function __construct(
        ProductRepository $productRepository
    ){
        $this->productRepository = $productRepository;
    }

    public function isNotifyEligible(NotificationRequestDto $notificationRequestDto): bool
    {
        $payload = $notificationRequestDto->payload;

        $size = $payload['size'];

        $productId = $payload['product'];

        $product = $this->productRepository->find($productId);

        return false === $product->isSizeAvailable($size);
    }

    public function getType(): string
    {
        return Notification::TYPE_SIZE_AVAILABLE;
    }
}
