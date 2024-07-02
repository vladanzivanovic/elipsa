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
    public function __construct(
        private readonly OrderRequestParser $requestParser,
        private readonly OrderHandler $orderHandler,
        private readonly OrderEditResponseFormatter $responseFormatter,
        private readonly ExceptionView $exceptionView
    ) {}

    #[Route(path: '/api/order/create', name: 'site_api.create_order', options: ['expose' => true], methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $order = $this->requestParser->create($request->attributes->get('_country'));

        $this->orderHandler->save($order, 'CreateOrder');

        return $this->json($this->responseFormatter->formatResponse(
            $order,
            $request->getLocale()
        ), Response::HTTP_CREATED);
    }

    #[Route(path: '/api/order/remove/{token}', name: 'site_api.remove_order', options: ['expose' => true], methods: ['DELETE'])]
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

    #[Route(path: '/api/order/{token}', name: 'site_api.get_order', options: ['expose' => true], methods: ['GET'])]
    public function getOrder(Request $request, string $token): JsonResponse
    {
        $order = $this->requestParser->findOrder($token);

        return $this->json($this->responseFormatter->formatResponse(
            $order,
            $request->getLocale()
        ), Response::HTTP_CREATED);
    }
}
