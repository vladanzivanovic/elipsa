<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api\Order;

use App\Repository\ShopOrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BellNotificationController extends AbstractController
{
    private ShopOrderRepository $orderRepository;

    public function __construct(
        ShopOrderRepository $orderRepository
    ){
        $this->orderRepository = $orderRepository;
    }

    #[Route(path: '/api/notifications/bell', name: 'admin.get_bell_notifications', methods: ['GET'], options: ['expose' => true])]
    public function getNotifications(): JsonResponse
    {
        try {

            $noOfOrders = $this->orderRepository->count(['visited' => false]);

            return $this->json([
                'orders' => $noOfOrders,
            ], Response::HTTP_OK);
        } catch (\Throwable $throwable) {
            return $this->json(null,
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
