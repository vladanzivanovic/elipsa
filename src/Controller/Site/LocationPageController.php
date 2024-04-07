<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\LocationPageCollector;
use App\Formatter\Site\LocationPageResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class LocationPageController extends AbstractController
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

    
    #[Route(path: ['rs' => '/prodavnica', 'en' => '/stores'], name: 'site.location_page', methods: ['GET'])]
    #[Template('Site/Pages/locationList.html.twig')]
    public function __invoke(Request $request): array
    {
        $collections = $this->pageCollector->collect($request->getLocale());

        return $this->responseFormatter->formatResponse($collections, $request->getLocale());
    }
}
