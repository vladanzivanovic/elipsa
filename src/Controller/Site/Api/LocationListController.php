<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Collector\LocationPageCollector;
use App\Formatter\Site\LocationPageResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LocationListController extends AbstractController
{
    private LocationPageCollector $pageCollector;

    private LocationPageResponseFormatter $responseFormatter;

    public function __construct(
        LocationPageCollector $pageCollector,
        LocationPageResponseFormatter $responseFormatter
    ) {
        $this->pageCollector = $pageCollector;
        $this->responseFormatter = $responseFormatter;
    }

    #[Route(path: '/api/stores', name: 'site_api.locations', options: ['expose' => true], methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $collections = $this->pageCollector->collect();

        return $this->json(
            $this->responseFormatter->formatResponse($collections, $request->getLocale()),
            Response::HTTP_OK
        );
    }
}
