<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Collector\ShopFilterCollector;
use App\Collector\ShopPageCollector;
use App\Formatter\Site\ShopListResponseFormatter;
use App\Request\Dto\ShopListRequestDto;
use App\Request\Dto\ShopPageOptionsDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ShopListController extends AbstractController
{
    private ShopPageCollector $collectors;

    private ShopListResponseFormatter $formatter;

    private ShopFilterCollector $filterCollector;

    public function __construct(
        ShopPageCollector $collectors,
        ShopListResponseFormatter $formatter,
        ShopFilterCollector $filterCollector
    ) {
        $this->collectors = $collectors;
        $this->formatter = $formatter;
        $this->filterCollector = $filterCollector;
    }

    #[Route(path: '/api/products', name: 'site_api.shop_page', methods: ['POST'], options: ['expose' => true])]
    public function index(
        ShopPageOptionsDto $shopPageOptionsDto,
        ShopListRequestDto $shopListRequestDto,
        Request $request
    ): JsonResponse {
        $locale = $request->getLocale();

        $data = $this->collectors->collectForApi($shopListRequestDto, $shopPageOptionsDto, $this->getUser());
        $filters = $this->filterCollector->collect($locale);

        return $this->json($this->formatter->formatResponse(
            $data,
            $locale,
            ['site.shop_page', 'site.trendy_page'],
            $shopListRequestDto,
            $shopPageOptionsDto,
            $filters,
            $this->getUser())
        );
    }
}
