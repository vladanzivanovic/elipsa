<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\Promotion;
use App\Entity\ShopOrder;
use App\Helper\ValidatorHelper;
use App\Repository\ShopOrderRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class OrderHandler
{
    public function __construct(
        private readonly ValidatorHelper $validator,
        private readonly ShopOrderRepository $orderRepository,
        private readonly RequestStack $requestStack
    ) {}

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws \Exception
     */
    public function save(ShopOrder $order, string $group = null): int
    {
        $errors = $this->validator->validate($order, null, $group);

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (null == $order->getId()) {
            $this->orderRepository->persist($order);
        }

        $this->orderRepository->flush();

        return $order->getId();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function remove(ShopOrder $order): void
    {
        $this->orderRepository->delete($order);

        $this->orderRepository->flush();
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setCoupon(Promotion $coupon): void
    {
        $order = $this->orderRepository->getByToken($this->requestStack->getSession()->get('order'));
        $order->setCoupon($coupon);

        $this->orderRepository->flush();
    }
}
