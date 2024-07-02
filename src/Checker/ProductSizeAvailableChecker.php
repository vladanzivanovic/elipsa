<?php

declare(strict_types=1);

namespace App\Checker;

use App\Entity\Notification;
use App\Repository\ProductOptionsRepository;
use App\Repository\ProductRepository;
use App\Request\Dto\NotificationRequestDto;

final class ProductSizeAvailableChecker implements NotificationCheckerInterface
{
    public function __construct(
        private readonly ProductOptionsRepository $productOptionsRepository
    ){}

    public function isNotifyEligible(NotificationRequestDto $notificationRequestDto): bool
    {
        $payload = $notificationRequestDto->payload;

        $size = $payload['size'];

        $productId = $payload['product'];

        $productOption = $this->productOptionsRepository->findOneBy(['product' => $productId, 'country' => $notificationRequestDto->country]);

        return false === $productOption->isSizeAvailable($size);
    }

    public function getType(): string
    {
        return Notification::TYPE_SIZE_AVAILABLE;
    }
}
