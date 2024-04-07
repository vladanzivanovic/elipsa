<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Entity\Promotion;
use App\Handler\Site\OrderHandler;
use App\Parser\Site\CartEditRequestParser;
use App\Repository\ShopOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CartEditController extends AbstractController
{
    private \App\Parser\Site\CartEditRequestParser $requestParser;
    private \App\Handler\Site\OrderHandler $orderHandler;
    private \App\Repository\ShopOrderRepository $orderRepository;

    public function __construct(
        CartEditRequestParser $requestParser,
        OrderHandler $orderHandler,
        ShopOrderRepository $orderRepository
    ) {
        $this->requestParser = $requestParser;
        $this->orderHandler = $orderHandler;
        $this->orderRepository = $orderRepository;
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    #[Route(path: '/api/order/update-products', name: 'site_api.update_order_products', methods: ['PUT'], options: ['expose' => '
                                     true'])]
    public function update(Request $request): JsonResponse
    {
        $order = $this->requestParser->parse($request);

        $this->orderHandler->save($order);

        return $this->json(null);
    }

    /**
     *
     * @throws \Exception
     */
    #[Route(path: '/api/order/set-coupon/{code}', name: 'site_api.set_order_coupon', methods: ['PATCH'], options: ['expose' => true])]
    public function setPromoCoupon(Promotion $coupon, Request $request): JsonResponse
    {
        $order = $this->orderRepository->getByToken($request->getSession()->get('order'));

        if (null !== $order->getCoupon()) {
            return $this->json(['message' => 'promo_coupon.used'], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        $now = (new \DateTime())->getTimestamp();

        if ($now < $coupon->getValidFrom()->getTimestamp()) {
            return $this->json(['message' => 'promo_coupon.not_active_yet'], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        if ($now > $coupon->getValidTo()->getTimestamp()) {
            return $this->json(['message' => 'promo_coupon.expired'], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        $this->orderHandler->setCoupon($coupon);

        return $this->json(['discount' => $coupon->getDiscount()]);
    }
}
