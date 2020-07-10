<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\CollaboratorHandler;
use App\Handler\Site\LoyaltyHandler;
use App\Parser\Site\CollaboratorRequestParser;
use App\Parser\Site\LoyaltyRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class CollaboratorController extends AbstractController
{
    /**
     * @var CollaboratorRequestParser
     */
    private $requestParser;

    /**
     * @var CollaboratorHandler
     */
    private $handler;

    /**
     * @param CollaboratorRequestParser $requestParser
     * @param CollaboratorHandler       $handler
     */
    public function __construct(
        CollaboratorRequestParser $requestParser,
        CollaboratorHandler $handler
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
    }

    /**
     * @Route("/api/add-collaborator", name="site_api.add_collaborator", methods={"POST"}, options={"expose": true})
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function add(Request $request): JsonResponse
    {
        if (!$this->isCsrfTokenValid('collaborator_form', $request->request->get('_csrf_token'))) {
            $this->createAccessDeniedException();
        }
        try {
            $collaborator = $this->requestParser->parse($request->request, $request->files);
        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->handler->save($collaborator);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}