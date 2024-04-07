<?php

declare(strict_types=1);

namespace App\Controller\Site\Api\Order;

use App\Formatter\Site\OrderEditResponseFormatter;
use App\Handler\Site\OrderHandler;
use App\Parser\Site\Order\OrderCouponParser;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OrderCouponController extends AbstractController
{
    private OrderCouponParser $orderCouponParser;

    private OrderEditResponseFormatter $responseFormatter;

    private ExceptionView $exceptionView;
    private OrderHandler $orderHandler;

    public function __construct(
        OrderCouponParser $orderCouponParser,
        OrderEditResponseFormatter $responseFormatter,
        ExceptionView $exceptionView,
        OrderHandler $orderHandler
    ) {
        $this->orderCouponParser = $orderCouponParser;
        $this->responseFormatter = $responseFormatter;
        $this->exceptionView = $exceptionView;
        $this->orderHandler = $orderHandler;
    }

    /**
     * @return JsonResponse
     */
    #[Route(path: '/api/order/coupon/{token}/{code}', name: 'site_api.add_order_coupon_code', methods: ['PUT'], options: ['expose' => true])]
    public function manage(Request $request, string $token, string $code): Response
    {
        try {
            $order = $this->orderCouponParser->parse($token, $code);

            $this->orderHandler->save($order, 'SetCoupon');

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

    /**
     * @return JsonResponse
     */
    #[Route(path: '/api/order/coupon/{token}/{code}', name: 'site_api.remove_order_coupon_code', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(Request $request, string $token, string $code): Response
    {
        try {
            $order = $this->orderCouponParser->parse($token, $code, true);

            $this->orderHandler->save($order);

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
