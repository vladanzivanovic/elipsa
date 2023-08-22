<?php

declare(strict_types=1);

namespace App\Controller\Site\Api\Order;

use App\Formatter\Site\OrderEditResponseFormatter;
use App\Handler\Site\OrderHandler;
use App\Parser\Site\Order\OrderCompleteParser;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OrderCompleteController extends AbstractController
{
    private ExceptionView $exceptionView;

    private OrderCompleteParser $orderCompleteParser;

    private OrderHandler $orderHandler;

    private OrderEditResponseFormatter $responseFormatter;

    private HttpClientInterface $client;

    private string $googleRecaptchaSecret;

    public function __construct(
        ExceptionView $exceptionView,
        OrderCompleteParser $orderCompleteParser,
        OrderHandler $orderHandler,
        OrderEditResponseFormatter $responseFormatter,
        HttpClientInterface $client,
        string $googleRecaptchaSecret
    ) {
        $this->exceptionView = $exceptionView;
        $this->orderCompleteParser = $orderCompleteParser;
        $this->orderHandler = $orderHandler;
        $this->responseFormatter = $responseFormatter;
        $this->client = $client;
        $this->googleRecaptchaSecret = $googleRecaptchaSecret;
    }

    /**
     * @Route("/api/order/complete/{token}", name="site_api.add_order_complete_code", methods={"POST"}, options={"expose": true})
     *
     * @return JsonResponse
     */
    public function complete(Request $request, string $token): Response
    {
        try {
            $body = json_decode($request->getContent(), true);
            $requestBag = new ParameterBag($body);

            $this->validateHuman($requestBag);

            $order = $this->orderCompleteParser->parse($token, $requestBag);

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
     * @param ParameterBag $parameterBag
     *
     * @throws TransportExceptionInterface
     */
    private function validateHuman(ParameterBag $parameterBag): void
    {
        $response = $this->client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $this->googleRecaptchaSecret,
                'response' => $parameterBag->get('recaptcha_response'),
            ]
        ]);

        $captchaResponse = json_decode($response->getContent(), true);

        if (
            true === $this->isCsrfTokenValid('order_complete', $parameterBag->get('_csrf_token')) &&
            true === $captchaResponse['success'] && $captchaResponse['score'] > 0.4 && $captchaResponse['action'] === 'complete_order'
        ) {
            return;
        }

        $this->createAccessDeniedException();
    }
}
