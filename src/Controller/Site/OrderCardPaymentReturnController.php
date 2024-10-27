<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Entity\ShopOrder;
use App\Handler\Site\OrderHandler;
use App\Parser\Site\Order\OrderRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class OrderCardPaymentReturnController extends AbstractController
{
    public function __construct(
        private readonly OrderRequestParser $orderRequestParser,
        private readonly OrderHandler $orderHandler,
    ){}

    #[Route(path: '/checkout/card/callback/{token}', name: 'site.checkout_completed_card_callback', options: ['expose' => true], methods: ['POST', 'GET'])]
    public function bankArtCallback(Request $request, string $token): void
    {
        $order = $this->orderRequestParser->findOrder($token);

        $payload = json_decode($request->getContent(), true);

        $cardStatus = match ($payload['result']) {
            'ERROR' => ShopOrder::CARD_STATUS_FAILED,
            'OK' => ShopOrder::CARD_STATUS_PRE_AUTH,
            default => ShopOrder::CARD_STATUS_DECLINED
        };

        $order->setTransactionData([$cardStatus => $payload]);

        $this->orderHandler->save($order);
    }

    #[Route(path: '/checkout/card/return/intesa/{token}', name: 'site.checkout_completed_card_return.intesa', options: ['expose' => true], methods: ['POST'])]
    public function intesaSuccess(Request $request, string $token): RedirectResponse
    {
        $order = $this->orderRequestParser->findOrder($token);

        $order->setStatus(ShopOrder::STATUS_PENDING);
        $order->setCardStatus(ShopOrder::CARD_STATUS_FAILED);
        $order->setTransactionData([ShopOrder::CARD_STATUS_FAILED => $request->request->all()]);

        $redirectUrl = $this->redirectToRoute('site.checkout_completed_unsuccessful', ['_locale' => $request->getLocale(), 'token' => $token]);

        if ($request->request->getString('Response') === 'Approved') {
            $order->setCardStatus(ShopOrder::CARD_STATUS_PRE_AUTH);
            $order->setTransactionData([ShopOrder::CARD_STATUS_PRE_AUTH => $request->request->all()]);
            $redirectUrl =  $this->redirectToRoute('site.checkout_completed_successful', ['_locale' => $request->getLocale(), 'token' => $token]);
        }

        $this->orderHandler->save($order);

        return $redirectUrl;
    }

    #[Route(path: '/checkout/card/return/bank-art/{token}', name: 'site.checkout_completed_card_return.bank-art', options: ['expose' => true], methods: ['GET'])]
    public function bankArtSuccess(Request $request, string $token): RedirectResponse
    {
        $order = $this->orderRequestParser->findOrder($token);

        $order->setStatus(ShopOrder::STATUS_PENDING);
        $order->setCardStatus(ShopOrder::CARD_STATUS_FAILED);

        $redirectUrl = $this->redirectToRoute('site.checkout_completed_unsuccessful', ['_locale' => $request->getLocale(), 'token' => $token]);

        if (
            isset($order->getTransactionData()[ShopOrder::CARD_STATUS_PRE_AUTH]) &&
            $order->getTransactionData()[ShopOrder::CARD_STATUS_PRE_AUTH]['result'] === 'OK'
        ) {
            $order->setCardStatus(ShopOrder::CARD_STATUS_PRE_AUTH);
            $redirectUrl =  $this->redirectToRoute('site.checkout_completed_successful', ['_locale' => $request->getLocale(), 'token' => $token]);
        }

        $this->orderHandler->save($order);

        return $redirectUrl;
    }
}
