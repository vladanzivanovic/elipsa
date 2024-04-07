<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\ShopOrder;
use App\Handler\Site\OrderHandler;
use App\Parser\IntesaPaymentParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class IntesaPaymentProcessController extends AbstractController
{
    private \App\Parser\IntesaPaymentParser $paymentParser;

    private \Symfony\Contracts\Translation\TranslatorInterface $translator;
    private \App\Handler\Site\OrderHandler $handler;

    public function __construct(
        IntesaPaymentParser  $paymentParser,
        TranslatorInterface $translator,
        OrderHandler $handler
    ) {

        $this->paymentParser = $paymentParser;
        $this->translator = $translator;
        $this->handler = $handler;
    }

    
    #[Route(path: '/api/intesa-post-auth/{id}', name: 'admin.intesa_post_auth_request', methods: ['GET'], options: ['expose' => true])]
    public function postAuth(ShopOrder $order): JsonResponse
    {
        return $this->doRequest($order, ShopOrder::CARD_TYPE_POST_AUTH, 'intesa.already_payed');
    }

    
    #[Route(path: '/api/intesa-refund/{id}', name: 'admin.intesa_refund_request', methods: ['GET'], options: ['expose' => true])]
    public function refund(ShopOrder $order): JsonResponse
    {
        return $this->doRequest($order, ShopOrder::CARD_TYPE_REFUND, 'intesa.already_refunded');
    }

    
    #[Route(path: '/api/intesa-void/{id}', name: 'admin.intesa_void_request', methods: ['GET'], options: ['expose' => true])]
    public function void(ShopOrder $order): JsonResponse
    {
        return $this->doRequest($order, ShopOrder::CARD_TYPE_VOID, 'intesa.already_void');
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function doRequest(
        ShopOrder $order,
        string $type,
        string $errorMessage
    ): JsonResponse {
        try {
            $this->paymentParser->parse($order, $type);
        } catch (BadRequestHttpException $badRequestHttpException) {
            return $this->json(['message' => $this->translator->trans($errorMessage, [], null, 'rs')], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->handler->save($order);

        return $this->json(null);
    }
}