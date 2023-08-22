<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Exception\UserException;
use App\Handler\Site\WishListHandler;
use App\Parser\WishListRequestParser;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class WishListEditController extends AbstractController
{
    private WishListRequestParser $wishListRequestParser;

    private WishListHandler $wishListHandler;

    private ExceptionView $exceptionView;

    public function __construct(
        WishListRequestParser $wishListRequestParser,
        WishListHandler $wishListHandler,
        ExceptionView $exceptionView
    ) {
        $this->wishListRequestParser = $wishListRequestParser;
        $this->wishListHandler = $wishListHandler;
        $this->exceptionView = $exceptionView;
    }

    /**
     * @Route("/api/toggle-wish/{productId}",
     *     name="site_api.toggle_wish",
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
    public function toggleItem(Request $request, int $productId): JsonResponse
    {
        try {
            if (null === $this->getUser()) {
                $userException = new UserException('login_required');

                $userException->setDomain('messages');

                throw $userException;
            }

            $wish = $this->wishListRequestParser->parse($productId, $this->getUser());

            $isAdded = $wish->getId() === null;

            $this->wishListHandler->toggle($wish);

            if (true === $isAdded) {
                return $this->json(null, Response::HTTP_CREATED);
            }

            return $this->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $this->exceptionView->view($throwable, $request->getLocale())],
                Response::HTTP_UNAUTHORIZED
            );
        }
    }
}
