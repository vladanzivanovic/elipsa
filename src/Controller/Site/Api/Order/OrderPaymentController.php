<?php

declare(strict_types=1);

namespace App\Controller\Site\Api\Order;

use App\Parser\Site\Order\OrderRequestParser;
use App\View\ExceptionView;
use App\View\OrderPaymentRequestView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderPaymentController extends AbstractController
{
    public function __construct(
        private readonly OrderRequestParser $orderRequestParser,
        private readonly ExceptionView $exceptionView,
        private readonly OrderPaymentRequestView $orderPaymentView
    ) {}

    /**
     * @return JsonResponse
     */
    #[Route(path: '/api/order/payment/{token}', name: 'site_api.get_order_payment', options: ['expose' => true], methods: ['GET'])]
    public function getPayment(string $token, Request $request): Response
    {
        try {
            $order = $this->orderRequestParser->findOrder($token);

            return $this->json($this->orderPaymentView->view(
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
