<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Collectors\ShopPageCollectors;
use App\Formatter\Site\ShopPageResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ShopListController extends AbstractController
{
    /**
     * @var ShopPageCollectors
     */
    private $collectors;

    /**
     * @var ShopPageResponseFormatter
     */
    private $formatter;

    /**
     * @param ShopPageCollectors        $collectors
     * @param ShopPageResponseFormatter $formatter
     */
    public function __construct(
        ShopPageCollectors $collectors,
        ShopPageResponseFormatter $formatter
    ) {
        $this->collectors = $collectors;
        $this->formatter = $formatter;
    }

    /**
     * @Route("/api/products/{page}/{searchData}", name="site_api.shop_page", methods={"GET"}, defaults={"page": 1, "searchData": null}, requirements={"searchData": ".*"}, options={"expose": true})
     *
     * @param Request      $request
     * @param int          $page
     * @param string|null  $searchData
     *
     * @return JsonResponse
     */
    public function index(Request $request, int $page, ?string $searchData): JsonResponse
    {
        $data = $this->collectors->collectForApi($request->getLocale(), $page, $searchData);

        return $this->json($this->formatter->formatResponse($data));
    }
}