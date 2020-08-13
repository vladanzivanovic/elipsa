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
    /**
     * @var IntesaPaymentParser
     */
    private $paymentParser;

    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var OrderHandler
     */
    private $handler;

    /**
     * @param IntesaPaymentParser $paymentParser
     * @param TranslatorInterface $translator
     * @param OrderHandler        $handler
     */
    public function __construct(
        IntesaPaymentParser  $paymentParser,
        TranslatorInterface $translator,
        OrderHandler $handler
    ) {

        $this->paymentParser = $paymentParser;
        $this->translator = $translator;
        $this->handler = $handler;
    }

    /**
     * @Route("/api/intesa-post-auth/{id}", name="admin.intesa_post_auth_request", methods={"GET"}, options={"expose": true})
     *
     * @param ShopOrder $order
     *
     * @return JsonResponse
     */
    public function postAuth(ShopOrder $order): JsonResponse
    {
        return $this->doRequest($order, ShopOrder::CARD_TYPE_POST_AUTH, 'intesa.already_payed');
    }

    /**
     * @Route("/api/intesa-refund/{id}", name="admin.intesa_refund_request", methods={"GET"}, options={"expose": true})
     *
     * @param ShopOrder $order
     *
     * @return JsonResponse
     */
    public function refund(ShopOrder $order): JsonResponse
    {
        return $this->doRequest($order, ShopOrder::CARD_TYPE_REFUND, 'intesa.already_refunded');
    }

    /**
     * @Route("/api/intesa-void/{id}", name="admin.intesa_void_request", methods={"GET"}, options={"expose": true})
     *
     * @param ShopOrder $order
     *
     * @return JsonResponse
     */
    public function void(ShopOrder $order): JsonResponse
    {
        return $this->doRequest($order, ShopOrder::CARD_TYPE_VOID, 'intesa.already_void');
    }

    /**
     * @param ShopOrder $order
     * @param string    $type
     * @param string    $errorMessage
     *
     * @return JsonResponse
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