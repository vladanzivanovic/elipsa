<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Handler\Site\WishListHandler;
use App\Parser\WishListRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class WishListEditController extends AbstractController
{
    /**
     * @var WishListRequestParser
     */
    private $wishListRequestParser;

    /**
     * @var WishListHandler
     */
    private $wishListHandler;

    /**
     * @param WishListRequestParser $wishListRequestParser
     * @param WishListHandler       $wishListHandler
     */
    public function __construct(
        WishListRequestParser $wishListRequestParser,
        WishListHandler $wishListHandler
    ) {
        $this->wishListRequestParser = $wishListRequestParser;
        $this->wishListHandler = $wishListHandler;
    }

    /**
     * @Route({
     *          "rs": "/api/toggle-wish/{id}",
     *          "en": "/api/toggle-wish/{id}"
     *     },
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
    public function toggleItem(Request $request): JsonResponse
    {
        $wish = $this->wishListRequestParser->parse($request->request, $this->getUser());

        $isAdded = $wish->getId() === null;

        $this->wishListHandler->toggle($wish);

        return $this->json(['is_added' => $isAdded]);
    }
}