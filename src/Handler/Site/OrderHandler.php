<?php

declare(strict_types=1);

namespace App\Handler\Site;

use App\Entity\PromotionCoupon;
use App\Entity\ShopOrder;
use App\Helper\ValidatorHelper;
use App\Model\EmailModel;
use App\Repository\ShopOrderRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class OrderHandler
{
    /**
     * @var ShopOrderRepository
     */
    private $orderRepository;

    /**
     * @var ValidatorHelper
     */
    private $validator;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param ValidatorHelper     $validator
     * @param ShopOrderRepository $orderRepository
     * @param SessionInterface    $session
     */
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
     * @param ShopOrder $order
     * @param bool      $shouldSendEmail
     *
     * @return int
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(ShopOrder $order, bool $shouldSendEmail = false): int
    {
        $errors = $this->validator->validate($order, null, "SetOrder");

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
     * @param PromotionCoupon $coupon
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setCoupon(PromotionCoupon $coupon): void
    {
        $order = $this->orderRepository->find($this->session->get('order'));
        $order->setCoupon($coupon);

        $this->orderRepository->flush();
    }

    private function prepareEmail(ShopOrder $order)
    {
        $model = new EmailModel();
    }
}