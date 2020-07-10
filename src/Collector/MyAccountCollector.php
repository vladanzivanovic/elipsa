<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\User;
use App\Repository\OrderProductRepository;
use App\Repository\UserWishesRepository;

final class MyAccountCollector
{
    /**
     * @var OrderProductRepository
     */
    private $orderProductRepository;
    /**
     * @var UserWishesRepository
     */
    private $wishesRepository;

    /**
     * MyAccountCollector constructor.
     *
     * @param OrderProductRepository $orderProductRepository
     * @param UserWishesRepository   $wishesRepository
     */
    public function __construct(
        OrderProductRepository $orderProductRepository,
        UserWishesRepository $wishesRepository
    ) {
        $this->orderProductRepository = $orderProductRepository;
        $this->wishesRepository = $wishesRepository;
    }

    /**
     * @param User   $user
     * @param string $locale
     *
     * @return array
     */
    public function collect(User $user, string $locale): array
    {
        $data = [
            'orders' => $this->orderProductRepository->getByUser($user, $locale),
            'wishes' => $this->wishesRepository->getByUser($user, $locale),
        ];

        return $data;
    }
}