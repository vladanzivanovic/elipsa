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
use App\Request\Dto\Admin\CatalogEditRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CatalogEditApiController extends AbstractController
{
    public function __construct(
        private readonly CatalogHandler $handler,
        private readonly CatalogEditRequestParser $requestParser
    ) {}

    #[Route(path: '/api/add-catalog', name: 'admin.add_catalog_api', options: ['expose' => true], methods: ['POST'])]
    public function add(CatalogEditRequestDto $catalogEditRequestDto): JsonResponse
    {
        $catalog = $this->requestParser->parse($catalogEditRequestDto);

        $this->handler->save($catalog);

        return $this->json(null, Response::HTTP_CREATED);
    }

    #[Route(path: '/api/update-catalog/{id}', name: 'admin.edit_catalog_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(CatalogEditRequestDto $catalogEditRequestDto, Catalogue $catalogue): JsonResponse
    {
        $this->requestParser->parse($catalogEditRequestDto, $catalogue);

        $this->handler->save($catalogue);

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     * @throws \ReflectionException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route(path: '/api/toggle-catalog-status/{id}/{status}', name: 'admin.api_toggle_catalog_status', options: ['expose' => true], methods: ['PATCH'])]
    public function toggleActivation(Catalogue $catalogue, int $status): JsonResponse
    {
        $catalogue->setStatus($status);

        $this->handler->save($catalogue);

        $statusText = ConstantsHelper::getConstantName((string) $status, 'STATUS', Catalogue::class);

        return $this->json(['text' => $statusText]);
    }
}
