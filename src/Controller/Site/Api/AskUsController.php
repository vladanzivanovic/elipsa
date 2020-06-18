<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\AskUsHandler;
use App\Handler\Site\LoyaltyHandler;
use App\Parser\Site\AskUsRequestParser;
use App\Parser\Site\LoyaltyRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class AskUsController extends AbstractController
{
    /**
     * @var AskUsRequestParser
     */
    private $requestParser;

    /**
     * @var AskUsHandler
     */
    private $handler;

    /**
     * @param AskUsRequestParser $requestParser
     * @param AskUsHandler       $handler
     */
    public function __construct(
        AskUsRequestParser $requestParser,
        AskUsHandler $handler
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
    }

    /**
     * @Route({
     *          "rs": "/api/ask-us",
     *          "en": "/api/ask-us"
     *      },
     *     name="site_api.ask_us",
     *     methods={"POST"},
     *     options={"expose": true}
     * )
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function add(Request $request): JsonResponse
    {
        $askUs = $this->requestParser->parse($request->request);

        $this->handler->save($askUs);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}