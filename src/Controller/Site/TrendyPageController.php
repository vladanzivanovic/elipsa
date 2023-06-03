<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\ShopPageCollector;
use App\Formatter\Site\ShopPageResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class TrendyPageController extends AbstractController
{
    private ShopPageCollector $collectors;

    private ShopPageResponseFormatter $formatter;

    public function __construct(
        ShopPageCollector $collectors,
        ShopPageResponseFormatter $formatter
    ) {
        $this->collectors = $collectors;
        $this->formatter = $formatter;
    }

    /**
     * @Route({
     *          "rs": "/trendovi/{page}/{searchData}",
     *          "en": "/trends/{page}/{searchData}"
     *     },
     *     name="site.trendy_page",
     *     methods={"GET"},
     *     defaults={"page": 1, "searchData": null},
     *     requirements={"searchData": ".*"},
     *     options={"expose": true}
     * )
     * @Template("Site/Pages/trendy.html.twig")
     *
     * @param Request $request
     * @param int     $page
     * @param string  $searchData
     *
     * @return array
     */
    public function index(Request $request, int $page, ?string $searchData): array
    {
        $locale = $request->getSession()->get('_locale');

        $data = $this->collectors->collect($locale, $page, $this->getUser(), $searchData, true);

        return $this->formatter->formatResponse($data, $locale, $request->attributes->get('_route'));
    }
}
