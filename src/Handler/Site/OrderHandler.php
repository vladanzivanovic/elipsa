<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\Promotion;
use App\Entity\ShopOrder;
use App\Helper\ValidatorHelper;
use App\Repository\ShopOrderRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class OrderHandler
{
    private ShopOrderRepository $orderRepository;

    private ValidatorHelper $validator;

    private SessionInterface $session;

    public function __construct(
        ValidatorHelper $validator,
        ShopOrderRepository $orderRepository,
        SessionInterface $session
    ) {
        $this->orderRepository = $orderRepository;
        $this->validator = $validator;
        $this->session = $session;
    }

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
     * @param Promotion $coupon
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setCoupon(Promotion $coupon): void
    {
        $order = $this->orderRepository->getByToken($this->session->get('order'));
        $order->setCoupon($coupon);

        $this->orderRepository->flush();
    }
}
