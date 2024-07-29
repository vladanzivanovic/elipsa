<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\LoyaltyHandler;
use App\Parser\Site\LoyaltyRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class LoyaltyController extends AbstractController
{
    private \App\Parser\Site\LoyaltyRequestParser $requestParser;

    private \App\Handler\Site\LoyaltyHandler $handler;

    public function __construct(
        LoyaltyRequestParser $requestParser,
        LoyaltyHandler $handler
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
    }

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    #[Route(path: '/api/add-loyalty', name: 'site_api.add_loyalty', methods: ['POST'], options: ['expose' => true])]
    public function add(Request $request): JsonResponse
    {
        try {
            $loyalty = $this->requestParser->parse($request->request);
            $newsLetter = $this->requestParser->parseNewsLetter($loyalty->getEmail());
        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->handler->save($loyalty, $newsLetter);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}
