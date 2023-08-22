<?php

declare(strict_types=1);

namespace App\Controller\Site\Api\Order;

use App\Parser\Site\Order\OrderRequestParser;
use App\View\ExceptionView;
use App\View\OrderPaymentView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OrderPaymentController extends AbstractController
{
    private OrderRequestParser $orderRequestParser;

    private ExceptionView $exceptionView;

    private OrderPaymentView $orderPaymentView;

    public function __construct(
        OrderRequestParser $orderRequestParser,
        ExceptionView $exceptionView,
        OrderPaymentView $orderPaymentView
    ) {
        $this->orderRequestParser = $orderRequestParser;
        $this->exceptionView = $exceptionView;
        $this->orderPaymentView = $orderPaymentView;
    }

    /**
     * @Route("/api/order/payment/{token}", name="site_api.get_order_payment", methods={"GET"}, options={"expose": true})
     *
     * @return JsonResponse
     */
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
