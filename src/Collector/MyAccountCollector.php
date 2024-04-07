<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\User;
use App\Repository\OrderProductRepository;
use App\Repository\UserWishesRepository;

final class MyAccountCollector
{
    private \App\Repository\OrderProductRepository $orderProductRepository;
    private \App\Repository\UserWishesRepository $wishesRepository;

    /**
     * MyAccountCollector constructor.
     */
    public function __construct(
        OrderProductRepository $orderProductRepository,
        UserWishesRepository $wishesRepository
    ) {
        $this->orderProductRepository = $orderProductRepository;
        $this->wishesRepository = $wishesRepository;
    }

    
    public function collect(User $user, string $locale): array
    {
        return [
            'orders' => $this->orderProductRepository->getByUser($user, $locale),
            'wishes' => $this->wishesRepository->getByUser($user, $locale),
        ];
    }
}