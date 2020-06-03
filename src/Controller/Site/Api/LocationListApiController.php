<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Collector\LocationPageCollector;
use App\Formatter\Site\LocationPageResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class LocationListApiController extends AbstractController
{
    /**
     * @var LocationPageCollector
     */
    private $pageCollector;

    /**
     * @var LocationPageResponseFormatter
     */
    private $responseFormatter;

    /**
     * @param LocationPageCollector         $pageCollector
     * @param LocationPageResponseFormatter $responseFormatter
     */
    public function __construct(
        LocationPageCollector $pageCollector,
        LocationPageResponseFormatter $responseFormatter
    ) {
        $this->pageCollector = $pageCollector;
        $this->responseFormatter = $responseFormatter;
    }

    /**
     * @Route("/api/location-list/{countryCode}", name="site_api.location_list", methods={"GET"}, options={"expose": true})
     *
     * @param string  $countryCode
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function __invoke(string $countryCode, Request $request): JsonResponse
    {
        $collections = $this->pageCollector->collect($request->getLocale(), $countryCode);
        $formattedResponse = $this->responseFormatter->formatResponse($collections);

        return $this->json($formattedResponse['locations']);
    }
}