<?php

declare(strict_types=1);

namespace App\Controller\Site\Api\Order;

use App\Formatter\Site\OrderEditResponseFormatter;
use App\Handler\Site\OrderHandler;
use App\Parser\Site\Order\OrderRequestParser;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OrderController extends AbstractController
{
    private OrderRequestParser $requestParser;

    private OrderHandler $orderHandler;

    private OrderEditResponseFormatter $responseFormatter;

    private ExceptionView $exceptionView;

    public function __construct(
        OrderRequestParser $requestParser,
        OrderHandler $orderHandler,
        OrderEditResponseFormatter $responseFormatter,
        ExceptionView $exceptionView
    ) {
        $this->requestParser = $requestParser;
        $this->orderHandler = $orderHandler;
        $this->responseFormatter = $responseFormatter;
        $this->exceptionView = $exceptionView;
    }

    #[Route(path: '/api/order/create', name: 'site_api.create_order', methods: ['POST'], options: ['expose' => true])]
    public function create(Request $request): JsonResponse
    {
        $order = $this->requestParser->create();

        $this->orderHandler->save($order, 'CreateOrder');

        return $this->json($this->responseFormatter->formatResponse(
            $order,
            $request->getLocale()
        ), Response::HTTP_CREATED);
    }

    #[Route(path: '/api/order/remove/{token}', name: 'site_api.remove_order', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(Request $request, string $token): JsonResponse
    {
        try {
        $order = $this->requestParser->findOrder($token);

        $this->orderHandler->remove($order);

        return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $this->exceptionView->view($throwable, $request->getLocale())],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route(path: '/api/order/{token}', name: 'site_api.get_order', methods: ['GET'], options: ['expose' => true])]
    public function getOrder(Request $request, string $token): JsonResponse
    {
        $order = $this->requestParser->findOrder($token);

        return $this->json($this->responseFormatter->formatResponse(
            $order,
            $request->getLocale()
        ), Response::HTTP_CREATED);
    }
}
