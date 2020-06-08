<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\LoyaltyHandler;
use App\Parser\Site\LoyaltyRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class RegistrationController extends AbstractController
{
    /**
     * @var LoyaltyRequestParser
     */
    private $requestParser;

    /**
     * @var LoyaltyHandler
     */
    private $handler;

    /**
     * LoyaltyController constructor.
     *
     * @param LoyaltyRequestParser $requestParser
     * @param LoyaltyHandler       $handler
     */
    public function __construct(
        LoyaltyRequestParser $requestParser,
        LoyaltyHandler $handler
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
    }

    /**
     * @Route("/api/add-user", name="site_api.user_registration", methods={"POST"}, options={"expose": true})
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function add(Request $request): JsonResponse
    {
        $csrf = $request->request->get('_csrf_token');

        if (false === $this->isCsrfTokenValid('user_registration', $csrf)) {
            $this->createAccessDeniedException();
        }

        try {
            $loyalty = $this->requestParser->parse($request->request);
        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->handler->save($loyalty);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}