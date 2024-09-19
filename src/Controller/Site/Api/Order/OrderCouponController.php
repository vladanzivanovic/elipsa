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
use Symfony\Component\Routing\Attribute\Route;

final class OrderCouponController extends AbstractController
{
    public function __construct(
        private readonly OrderCouponParser $orderCouponParser,
        private readonly OrderEditResponseFormatter $responseFormatter,
        private readonly ExceptionView $exceptionView,
        private readonly OrderHandler $orderHandler
    ) {}

    /**
     * @return JsonResponse
     */
    #[Route(path: '/api/order/coupon/{token}/{code}', name: 'site_api.add_order_coupon_code', options: ['expose' => true], methods: ['PUT'])]
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
    #[Route(path: '/api/order/coupon/{token}/{code}', name: 'site_api.remove_order_coupon_code', options: ['expose' => true], methods: ['DELETE'])]
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
