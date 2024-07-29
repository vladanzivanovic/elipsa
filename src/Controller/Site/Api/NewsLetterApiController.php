<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\NewsLetterHandler;
use App\Parser\Site\NewsLetterRequestParser;
use App\Request\Dto\NewsLetterRequestDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class NewsLetterApiController extends AbstractController
{
    private NewsLetterRequestParser $requestParser;

    private NewsLetterHandler $handler;

    public function __construct(
        NewsLetterRequestParser $requestParser,
        NewsLetterHandler $handler
    ) {
        $this->requestParser = $requestParser;
        $this->handler = $handler;
    }

    /**
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/news-letter-add-user', name: 'site_api.news_letter_add_user', methods: ['POST'], options: ['expose' => true])]
    public function addEmail(NewsLetterRequestDto $newsLetterRequestDto): JsonResponse
    {
        try {
            $newsLetter = $this->requestParser->parse($newsLetterRequestDto);
            $hasLoyalty = $this->handler->save($newsLetter);
        } catch (BadRequestHttpException $exception) {
            return $this->json(['message' => $exception->getMessage()], $exception->getStatusCode());
        }

        return $this->json(['hasLoyalty' => $hasLoyalty], Response::HTTP_CREATED);
    }
}
