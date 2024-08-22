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

        $order->setTransactionData([ShopOrder::CARD_STATUS_PRE_AUTH => json_decode($request->getContent(), true)]);

        $this->orderHandler->save($order);
    }

    #[Route(path: '/checkout/card/return/intesa/{token}', name: 'site.checkout_completed_card_return.intesa', options: ['expose' => true], methods: ['POST'])]
    public function intesaSuccess(Request $request, string $token): RedirectResponse
    {
        $order = $this->orderRequestParser->findOrder($token);

        $order->setStatus(ShopOrder::STATUS_PENDING);
        $order->setTransactionData([$request->request->getString('Response') => $request->request->all()]);

        $redirectUrl = $this->redirectToRoute('site.checkout_completed_unsuccessful', ['_locale' => $request->getLocale(), 'token' => $token]);

        if ($request->request->getString('Response') === 'Approved') {
            $order->setTransactionData([ShopOrder::CARD_STATUS_PRE_AUTH => $request->request->all()]);
            $redirectUrl =  $this->redirectToRoute('site.checkout_completed_successful', ['_locale' => $request->getLocale(), 'token' => $token]);
        }

        $this->orderHandler->save($order);

        return $redirectUrl;
    }
}
