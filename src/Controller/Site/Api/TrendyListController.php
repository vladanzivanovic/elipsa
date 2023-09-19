<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Collector\ShopPageCollector;
use App\Formatter\Site\ShopListResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class TrendyListController extends AbstractController
{
    private ShopPageCollector $collectors;

    private ShopListResponseFormatter $formatter;

    public function __construct(
        ShopPageCollector $collectors,
        ShopListResponseFormatter $formatter
    ) {
        $this->collectors = $collectors;
        $this->formatter = $formatter;
    }

    /**
     * @Route({
     *          "rs": "/api/trendy/{page}/{searchData}",
     *          "en": "/api/trendy/{page}/{searchData}"
     *      },
     *     name="site_api.trendy_page",
     *     methods={"GET"},
     *     defaults={"page": 1, "searchData": null},
     *     requirements={"searchData": ".*"},
     *     options={"expose": true}
     * )
     *
     * @param Request      $request
     * @param int          $page
     * @param string|null  $searchData
     *
     * @return JsonResponse
     */
    public function index(Request $request, int $page, ?string $searchData): JsonResponse
    {
        $locale = $request->getLocale();

        $data = $this->collectors->collectForApi($locale, $page, $this->getUser(), $searchData, true);

        return $this->json($this->formatter->formatResponse($data, $locale, 'site.trendy_page', $this->getUser()));
    }
}
