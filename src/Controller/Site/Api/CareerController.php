<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\CareerHandler;
use App\Parser\Site\CareerRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class CareerController extends AbstractController
{
    /**
     * @var CareerRequestParser
     */
    private $requestParser;

    /**
     * @var CareerHandler
     */
    private $handler;

    /**
     * @param CareerRequestParser $requestParser
     * @param CareerHandler       $handler
     */
    public function __construct(
        CareerRequestParser $requestParser,
        CareerHandler $handler
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
    }

    /**
     * @Route("/api/add-career", name="site_api.add_career", methods={"POST"}, options={"expose": true})
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function add(Request $request): JsonResponse
    {
        if (!$this->isCsrfTokenValid('career_form', $request->request->get('_csrf_token'))) {
            $this->createAccessDeniedException();
        }
        try {
            $career = $this->requestParser->parse($request->request, $request->files);
        } catch (BadRequestHttpException $httpException) {
            return $this->json(['error' => $httpException->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->handler->save($career);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}