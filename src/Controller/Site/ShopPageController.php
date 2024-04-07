<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\ShopFilterCollector;
use App\Collector\ShopPageCollector;
use App\Formatter\Site\ShopListResponseFormatter;
use App\Request\Dto\ShopListRequestDto;
use App\Request\Dto\ShopPageOptionsDto;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ShopPageController extends AbstractController
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

    #[Route(path: ['rs' => '/proizvodi', 'en' => '/products', 'ba' => '/proizvodi'], name: 'site.shop_page', options: ['expose' => true], defaults: ['page' => 1], methods: ['GET'])]
    #[Template('Site/Pages/shop.html.twig')]
    public function shopPage(
        ShopPageOptionsDto $shopPageOptionsDto,
        ShopListRequestDto $shopListRequestDto,
        Request $request
    ): array {
        return $this->generate(
            $shopPageOptionsDto,
            $shopListRequestDto,
            $request
        );
    }

    
    #[Route(path: ['rs' => '/trendovi', 'en' => '/trends', 'ba' => '/trendovi'], name: 'site.trendy_page', options: ['expose' => true], defaults: ['page' => 1], methods: ['GET'])]
    #[Template('Site/Pages/shop.html.twig')]
    public function trendyPage(
        ShopPageOptionsDto $shopPageOptionsDto,
        ShopListRequestDto $shopListRequestDto,
        Request $request
    ): array {
        return $this->generate(
            $shopPageOptionsDto,
            $shopListRequestDto,
            $request
        );
    }

    private function generate(
        ShopPageOptionsDto $shopPageOptionsDto,
        ShopListRequestDto $shopListRequestDto,
        Request $request
    ): array {
        $data = $this->collectors->collect($shopListRequestDto, $shopPageOptionsDto, $this->getUser());
        $filters = $this->filterCollector->collect($shopPageOptionsDto->locale);

        return $this->formatter->formatResponse(
            $data,
            $shopPageOptionsDto->locale,
            [$request->attributes->get('_route')],
            $shopListRequestDto,
            $shopPageOptionsDto,
            $filters,
            $this->getUser()
        );
    }
}
