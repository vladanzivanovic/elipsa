<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Banner;
use App\Entity\Catalogue;
use App\Entity\Slider;
use App\Handler\BannerHandler;
use App\Helper\ConstantsHelper;
use App\Handler\CatalogHandler;
use App\Parser\CatalogEditRequestParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class CatalogEditApiController extends AbstractController
{
    private CatalogHandler $handler;

    private CatalogEditRequestParser $requestParser;

    public function __construct(
        CatalogHandler $handler,
        CatalogEditRequestParser $requestParser
    ) {
        $this->handler = $handler;
        $this->requestParser = $requestParser;
    }

    /**
     * @Route("/api/add-catalog", name="admin.add_catalog_api", methods={"POST"}, options={"expose": true})
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function add(Request $request): JsonResponse
    {
        $catalog = $this->requestParser->parse($request->request);

        $this->handler->save($catalog);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/update-catalog/{id}", name="admin.edit_catalog_api", methods={"PUT"}, options={"expose": true})
     *
     * @param Request   $request
     *
     * @param Catalogue $catalogue
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Request $request, Catalogue $catalogue): JsonResponse
    {
        $this->requestParser->parse($request->request, $catalogue);

        $this->handler->save($catalogue);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/toggle-catalog-status/{id}/{status}", name="admin.api_toggle_catalog_status", methods={"PATCH"},
     *                                                   options={"expose": true})
     *
     * @param Catalogue $catalogue
     * @param int       $status
     *
     * @return JsonResponse
     *
     * @throws \ReflectionException
     */
    public function toggleActivation(Catalogue $catalogue, int $status): JsonResponse
    {
        $catalogue->setStatus((int) $status);

        $this->handler->save($catalogue);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Catalogue::class);

        return $this->json(['text' => $statusText]);
    }
}
