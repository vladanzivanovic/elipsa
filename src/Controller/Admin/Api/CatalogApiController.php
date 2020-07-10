<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\Slider;
use App\Handler\BannerHandler;
use App\Helper\ConstantsHelper;
use App\Handler\CatalogHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CatalogApiController extends AbstractController
{
    /**
     * @var CatalogHandler
     */
    private $handler;

    /**
     * @param CatalogHandler $handler
     */
    public function __construct(
        CatalogHandler $handler
    ) {
        $this->handler = $handler;
    }

    /**
     * @Route("/api/set-catalog", name="admin.set_catalog", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function set(Request $request): JsonResponse
    {
        $this->handler->setImages(json_decode($request->request->get('images'), true));

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }
}