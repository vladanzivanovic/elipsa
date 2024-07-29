<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api\Order;

use App\Handler\Site\OrderHandler;
use App\Mailer\OrderStatusChangeMailer;
use App\Parser\OrderStatusParser;
use App\Request\Dto\OrderStateRequestDto;
use App\View\OrderView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderStatusChangeController extends AbstractController
{
    private OrderStatusParser $orderStateParser;

    private OrderHandler $orderHandler;

    private OrderStatusChangeMailer $orderStatusChangeMailer;

    private OrderView $orderView;

    public function __construct(
        OrderStatusParser $orderStateParser,
        OrderHandler $orderHandler,
        OrderStatusChangeMailer $orderStatusChangeMailer,
        OrderView $orderView
    ){
        $this->orderStateParser = $orderStateParser;
        $this->orderHandler = $orderHandler;
        $this->orderStatusChangeMailer = $orderStatusChangeMailer;
        $this->orderView = $orderView;
    }

    #[Route(path: '/api/order/{token}/status', name: 'admin.order_set_status', methods: ['PUT'], options: ['expose' => true])]
    public function changeState(OrderStateRequestDto $orderStateRequestDto): JsonResponse
    {
        try {
            $order = $this->orderStateParser->parse($orderStateRequestDto);

            $this->orderHandler->save($order, 'SetStatus');

            $viewData = $this->orderView->view($order, $orderStateRequestDto->locale);

            $this->orderStatusChangeMailer->sendEmail(
                $viewData,
                $order,
                $orderStateRequestDto->locale
            );

            return $this->json(['message' => 'Status porudžbine je promenjen'], Response::HTTP_OK);
        } catch (\Throwable $throwable) {
            return $this->json(null,
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
