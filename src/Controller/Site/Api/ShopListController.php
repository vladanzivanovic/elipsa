<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Collector\ShopPageCollector;
use App\Formatter\Site\ShopPageResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ShopListController extends AbstractController
{
    /**
     * @var ShopPageCollector
     */
    private $collectors;

    /**
     * @var ShopPageResponseFormatter
     */
    private $formatter;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param ShopPageCollector         $collectors
     * @param ShopPageResponseFormatter $formatter
     * @param SessionInterface          $session
     */
    public function __construct(
        ShopPageCollector $collectors,
        ShopPageResponseFormatter $formatter,
        SessionInterface $session
    ) {
        $this->collectors = $collectors;
        $this->formatter = $formatter;
        $this->session = $session;
    }

    /**
     * @Route({
     *          "rs": "/api/products/{page}/{searchData}",
     *          "en": "/api/products/{page}/{searchData}"
     *      },
     *     name="site_api.shop_page",
     *     methods={"GET"},
     *     defaults={"page": 1, "searchData": null},
     *     requirements={"searchData": ".*"},
     *     options={"expose": true}
     * )
     *
     * @param int          $page
     * @param string|null  $searchData
     *
     * @return JsonResponse
     */
    public function index(Request $request, int $page, ?string $searchData): JsonResponse
    {
        $locale = $request->getLocale();

        $data = $this->collectors->collectForApi($locale, $page, $searchData);

        return $this->json($this->formatter->formatResponse($data, $locale, 'site.shop_page'));
    }
}