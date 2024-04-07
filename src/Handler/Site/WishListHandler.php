<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\UserWishes;
use App\Repository\UserWishesRepository;

final class WishListHandler
{
    private \App\Repository\UserWishesRepository $userWishesRepository;

    public function __construct(
        UserWishesRepository $userWishesRepository
    ) {
        $this->userWishesRepository = $userWishesRepository;
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function toggle(UserWishes $userWishes): void
    {
        if (null === $userWishes->getId()) {
            $this->userWishesRepository->persist($userWishes);

        } else {
            $this->userWishesRepository->delete($userWishes);
        }

        $this->userWishesRepository->flush();
    }
}