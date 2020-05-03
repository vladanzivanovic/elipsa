<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Entity\PromotionCoupon;
use App\Handler\Site\OrderHandler;
use App\Parser\Site\CartEditRequestParser;
use App\Repository\ShopOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CartEditController extends AbstractController
{
    /**
     * @var CartEditRequestParser
     */
    private $requestParser;
    /**
     * @var OrderHandler
     */
    private $orderHandler;
    /**
     * @var ShopOrderRepository
     */
    private $orderRepository;

    /**
     * @param CartEditRequestParser $requestParser
     * @param OrderHandler          $orderHandler
     * @param ShopOrderRepository   $orderRepository
     */
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
     * @Route("/api/order/update-products", name="site_api.update_order_products", methods={"PUT"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Request $request): JsonResponse
    {
        $order = $this->requestParser->parse($request);

        $this->orderHandler->save($order);

        return $this->json(null);
    }

    /**
     * @Route("/api/order/set-coupon/{code}", name="site_api.set_order_coupon", methods={"PATCH"}, options={"expose": true})
     *
     * @param PromotionCoupon $coupon
     * @param Request         $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function setPromoCoupon(PromotionCoupon $coupon, Request $request): JsonResponse
    {
        $order = $this->orderRepository->find($request->getSession()->get('order'));

        if (null !== $order->getCoupon()) {
            return $this->json(['message' => 'promo_coupon.used'], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        $now = new \DateTime();

        if ($now < $coupon->getValidFrom()) {
            return $this->json(['message' => 'promo_coupon.not_active_yet'], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        if ($now > $coupon->getValidTo()) {
            return $this->json(['message' => 'promo_coupon.expired'], JsonResponse::HTTP_NOT_ACCEPTABLE);
        }

        $this->orderHandler->setCoupon($coupon);

        return $this->json(['discount' => $coupon->getDiscount()]);
    }
}