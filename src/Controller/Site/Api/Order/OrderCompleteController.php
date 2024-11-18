<?php

declare(strict_types=1);

namespace App\Controller\Site\Api\Order;

use App\Entity\User;
use App\Formatter\Site\OrderEditResponseFormatter;
use App\Formatter\Site\UserRegistrationFormatter;
use App\Handler\Site\OrderHandler;
use App\Mailer\UserRegistrationMailer;
use App\Parser\Site\Order\OrderCompleteParser;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class OrderCompleteController extends AbstractController
{
    public function __construct(
        private readonly ExceptionView $exceptionView,
        private readonly OrderCompleteParser $orderCompleteParser,
        private readonly OrderHandler $orderHandler,
        private readonly OrderEditResponseFormatter $responseFormatter,
        private readonly HttpClientInterface $client,
        private readonly UserRegistrationFormatter $userRegistrationFormatter,
        private readonly UserRegistrationMailer $userRegistrationMailer,
        private readonly string $googleRecaptchaSecret
    ) {}

    /**
     * @return JsonResponse
     */
    #[Route(path: '/api/order/complete/{token}', name: 'site_api.order_complete', options: ['expose' => true], methods: ['POST'])]
    public function complete(Request $request, string $token): Response
    {
        $locale = $request->attributes->get('_locale');

        try {
            $body = json_decode($request->getContent(), true);
            $requestBag = new ParameterBag($body);

            $this->validateHuman($requestBag);

            $order = $this->orderCompleteParser->parse($token, $requestBag);

            $this->orderHandler->save($order, 'SetCoupon');

            $response =  $this->responseFormatter->formatResponse(
                $order,
                $request->getLocale()
            );

            if ( null !== $order->getUser()->getResetToken()) {
                $this->sendUserRegistrationEmail($order->getUser(), $locale);
            }

            return $this->json($response, Response::HTTP_OK);
        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $this->exceptionView->view($throwable, $request->getLocale())],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    /**
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
            $this->isCsrfTokenValid('order_complete', $parameterBag->get('_csrf_token')) &&
            true === $captchaResponse['success'] && $captchaResponse['score'] > 0.4 && $captchaResponse['action'] === 'complete_order'
        ) {
            return;
        }

        $this->createAccessDeniedException();
    }

    private function sendUserRegistrationEmail(User $user, string $locale): void
    {
        $userFormattedData = $this->userRegistrationFormatter->formatResponse($user, $locale);

        $this->userRegistrationMailer->sendEmail($userFormattedData, $user);
    }
}
