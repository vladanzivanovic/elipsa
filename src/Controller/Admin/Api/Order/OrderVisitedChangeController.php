<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api\Order;

use App\Handler\Site\OrderHandler;
use App\Parser\OrderVisitedParser;
use App\Request\Dto\OrderRequestDto;
use App\View\OrderView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OrderVisitedChangeController extends AbstractController
{
    private OrderVisitedParser $orderVisitedParser;

    private OrderHandler $orderHandler;

    private OrderView $orderView;

    public function __construct(
        OrderVisitedParser $orderVisitedParser,
        OrderHandler $orderHandler,
        OrderView $orderView
    ){
        $this->orderVisitedParser = $orderVisitedParser;
        $this->orderHandler = $orderHandler;
        $this->orderView = $orderView;
    }

    #[Route(path: '/api/order/{token}/visited', name: 'admin.order_set_visited', methods: ['PUT'], options: ['expose' => true])]
    public function changeState(OrderRequestDto $orderRequestDto): JsonResponse
    {
        try {
            $order = $this->orderVisitedParser->parse($orderRequestDto);

            $this->orderHandler->save($order, 'SetVisited');

            $viewData = $this->orderView->view($order, $orderRequestDto->locale);

            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $throwable) {
            return $this->json(null,
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
