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
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    #[Route(path: '/api/add-catalog', name: 'admin.add_catalog_api', methods: ['POST'], options: ['expose' => true])]
    public function add(Request $request): JsonResponse
    {
        $catalog = $this->requestParser->parse($request->request);

        $this->handler->save($catalog);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     *
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/update-catalog/{id}', name: 'admin.edit_catalog_api', methods: ['PUT'], options: ['expose' => true])]
    public function update(Request $request, Catalogue $catalogue): JsonResponse
    {
        $this->requestParser->parse($request->request, $catalogue);

        $this->handler->save($catalogue);

        return $this->json(null, JsonResponse::HTTP_CREATED);
    }

    /**
     *
     *
     * @throws \ReflectionException
     */
    #[Route(path: '/api/toggle-catalog-status/{id}/{status}', name: 'admin.api_toggle_catalog_status', methods: ['PATCH'], options: ['expose' => true])]
    public function toggleActivation(Catalogue $catalogue, int $status): JsonResponse
    {
        $catalogue->setStatus($status);

        $this->handler->save($catalogue);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Catalogue::class);

        return $this->json(['text' => $statusText]);
    }
}
