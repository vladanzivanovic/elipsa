<?php

declare(strict_types=1);

namespace App\Controller\Site\Api\Order;

use App\Formatter\Site\OrderEditResponseFormatter;
use App\Handler\Site\OrderHandler;
use App\Handler\Site\OrderProductHandler;
use App\Parser\Site\Order\OrderProductRequestParser;
use App\Request\Dto\OrderProductRequestDto;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;

final class OrderProductController extends AbstractController
{
    public function __construct(
        private readonly OrderProductRequestParser $orderProductRequestParser,
        private readonly OrderHandler $orderHandler,
        private readonly OrderEditResponseFormatter $responseFormatter,
        private readonly OrderProductHandler $orderProductHandler,
        private readonly ExceptionView $exceptionView
    ) {}

    #[Route(path: '/api/order/product/{token}/{slug}', name: 'site_api.set_product_order', options: ['expose' => true], methods: ['POST'])]
    public function manage(OrderProductRequestDto $orderProductRequestDto): JsonResponse
    {
        try {
            $order = $this->orderProductRequestParser->parse($orderProductRequestDto);

            $this->orderHandler->save($order, 'SetProduct');

            return $this->json($this->responseFormatter->formatResponse(
                $order,
                $orderProductRequestDto->locale
            ), Response::HTTP_CREATED);
        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $this->exceptionView->view($throwable, $orderProductRequestDto->locale)],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    #[Route(path: '/api/order/product/{token}/{orderProductId}', name: 'site_api.remove_order_product', options: ['expose' => true], methods: ['DELETE'])]
    public function remove(Request $request, string $token, int $orderProductId): JsonResponse
    {
        try {
            $order = $this->orderProductRequestParser->getOrder($token);

            $orderProduct = $order->getOrderProductById($orderProductId);

            Assert::notNull($orderProduct);

            $this->orderProductHandler->removeProduct($orderProduct);

            return $this->json($this->responseFormatter->formatResponse(
                $order,
                $request->getLocale()
            ), Response::HTTP_OK);
        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $this->exceptionView->view($throwable, $request->getLocale())],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
