<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\User;
use App\Entity\UserWishes;
use App\Repository\ProductRepository;
use App\Repository\UserWishesRepository;
use Symfony\Component\HttpFoundation\ParameterBag;

final class WishListRequestParser
{
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var UserWishesRepository
     */
    private $wishesRepository;

    /**
     * @param ProductRepository    $productRepository
     * @param UserWishesRepository $wishesRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        UserWishesRepository $wishesRepository
    ) {
        $this->productRepository = $productRepository;
        $this->wishesRepository = $wishesRepository;
    }

    /**
     * @param ParameterBag $bag
     * @param User         $user
     *
     * @return UserWishes
     */
    public function parse(int $productId, User $user): UserWishes
    {
        $product = $this->productRepository->find($productId);
        $wish = $this->wishesRepository->findOneBy(['user' => $user, 'product' => $product]);

        if (null === $wish) {
            $wish = new UserWishes();
            $wish->setUser($user)
                ->setProduct($product);
        }

        return $wish;
    }
}
