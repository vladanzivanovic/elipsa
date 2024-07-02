<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\User;
use App\Formatter\Site\Router\ShopPageRouterFormatter;
use App\Request\Dto\ShopListRequestDto;
use App\Request\Dto\ShopPageOptionsDto;
use App\View\TagView;

final class ShopListResponseFormatter
{
    use FormatterTrait;

    public function __construct(
        private readonly ProductFormatter $productFormatter,
        private readonly TagView $tagView,
        private readonly ShopPageRouterFormatter $shopPageRouterFormatter,
        private readonly ShopFilterFormatter $shopFilterFormatter
    ) {}

    public function formatResponse(
        array $data,
        array $routeNames,
        ShopListRequestDto $shopListRequestDto,
        ShopPageOptionsDto $shopPageOptionsDto,
        array $filters,
        ?User $user = null
    ): array {
        $products = $this->productFormatter->getProducts($data['data'], $shopPageOptionsDto->country, $user);

        $localizedUrls = $this->shopPageRouterFormatter->createLocalizedLinks(
            $shopPageOptionsDto,
            $shopListRequestDto,
            $routeNames
        );

        return [
            'products' => $products,
            'pagination' => $data['pagination'],
            'filters' => $this->shopFilterFormatter->format($filters),
            'search' => $shopListRequestDto->toArray(),
            'page_options' => $shopPageOptionsDto->toArray(),
            '_web_links' => $localizedUrls,
        ];
    }
}
