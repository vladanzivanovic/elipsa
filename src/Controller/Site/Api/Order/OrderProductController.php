<?php

declare(strict_types=1);

namespace App\Controller\Site\Api\Order;

use App\Formatter\Site\OrderEditResponseFormatter;
use App\Handler\Site\OrderHandler;
use App\Handler\Site\OrderProductHandler;
use App\Parser\Site\Order\OrderProductRequestParser;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\Assert\Assert;

final class OrderProductController extends AbstractController
{
    private OrderProductRequestParser $orderProductRequestParser;

    private OrderHandler $orderHandler;

    private OrderEditResponseFormatter $responseFormatter;

    private OrderProductHandler $orderProductHandler;

    private ExceptionView $exceptionView;

    public function __construct(
        OrderProductRequestParser $orderProductRequestParser,
        OrderHandler $orderHandler,
        OrderEditResponseFormatter $responseFormatter,
        OrderProductHandler $orderProductHandler,
        ExceptionView $exceptionView
    ) {
        $this->orderHandler = $orderHandler;
        $this->responseFormatter = $responseFormatter;
        $this->orderProductRequestParser = $orderProductRequestParser;
        $this->orderProductHandler = $orderProductHandler;
        $this->exceptionView = $exceptionView;
    }

    /**
     * @Route("/api/order/product/{token}/{slug}", name="site_api.set_product_order", methods={"POST"}, options={"expose": true})
     *
     * @return JsonResponse
     */
    public function manage(Request $request, string $token, string $slug): JsonResponse
    {
        try {
            $order = $this->orderProductRequestParser->parse($request, $token, $slug);

            $this->orderHandler->save($order, 'SetProduct');

            return $this->json($this->responseFormatter->formatResponse(
                $order,
                $request->getLocale()
            ), Response::HTTP_CREATED);
        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $this->exceptionView->view($throwable, $request->getLocale())],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
     * @Route("/api/order/product/{token}/{orderProductId}", name="site_api.remove_order_product", methods={"DELETE"}, options={"expose": true})
     *
     * @return JsonResponse
     */
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
