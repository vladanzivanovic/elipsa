<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\LocationPageCollector;
use App\Formatter\Site\LocationPageResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class LocationPageController extends AbstractController
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
     * LocationPageController constructor.
     *
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
     * @Route({
     *          "rs": "/lokacije-prodavnica",
     *          "en": "/store-locations",
     *      },
     *     name="site.location_page",
     *     methods={"GET"}
     * )
     * @Template("Site/Pages/locationList.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function __invoke(Request $request): array
    {
        $collections = $this->pageCollector->collect($request->getLocale());

        return $this->responseFormatter->formatResponse($collections);
    }
}