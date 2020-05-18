<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Entity\OrderProduct;
use App\Handler\Site\OrderHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class OrderRemoveController extends AbstractController
{
    /**
     * @var OrderHandler
     */
    private $orderHandler;

    /**
     * @param OrderHandler $orderHandler
     */
    public function __construct(
        OrderHandler $orderHandler
    ) {
        $this->orderHandler = $orderHandler;
    }

    /**
     * @Route("/api/order/remove/{id}", name="site_api.remove_order_product", methods={"DELETE"}, options={"expose": true})
     *
     * @param OrderProduct $orderProduct
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(OrderProduct $orderProduct): JsonResponse
    {
        $this->orderHandler->removeProduct($orderProduct);

        return $this->json([], JsonResponse::HTTP_OK);
    }
}