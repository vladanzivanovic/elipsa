<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\NewsLetterHandler;
use App\Parser\Site\NewsLetterRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class NewsLetterApiController extends AbstractController
{
    /**
     * @var NewsLetterRequestParser
     */
    private $requestParser;
    /**
     * @var NewsLetterHandler
     */
    private $handler;

    /**
     * @param NewsLetterRequestParser $requestParser
     * @param NewsLetterHandler       $handler
     */
    public function __construct(
        NewsLetterRequestParser $requestParser,
        NewsLetterHandler $handler
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
    }

    /**
     * @Route({
     *          "rs": "/api/news-letter-add-user",
     *          "en": "/api/news-letter-add-user"
     *     },
     *     name="site_api.news_letter_add_user",
     *     methods={"POST"},
     *     options={"expose": true}
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addEmail(Request $request): JsonResponse
    {
        try {
            $newsLetter = $this->requestParser->parse($request->request);
            $hasLoyalty = $this->handler->save($newsLetter);
        } catch (BadRequestHttpException $exception) {
            return $this->json(['message' => $exception->getMessage()], $exception->getStatusCode());
        }

        return $this->json(['hasLoyalty' => $hasLoyalty], JsonResponse::HTTP_CREATED);
    }
}