<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Entity\User;
use App\Formatter\Options\TagOptionsFormatter;
use App\Formatter\Site\Router\ShopPageRouterFormatter;
use App\Request\Dto\ShopListRequestDto;
use App\Request\Dto\ShopPageOptionsDto;
use App\View\TagView;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

final class ShopListResponseFormatter
{
    use FormatterTrait;

    private RouterInterface $router;

    private ParameterBagInterface $bag;

    private SessionInterface $session;

    private ProductFormatter $productFormatter;

    private TagView $tagView;

    private TagOptionsFormatter $tagOptionsFormatter;

    private ShopPageRouterFormatter $shopPageRouterFormatter;

    private ShopFilterFormatter $shopFilterFormatter;

    public function __construct(
        RouterInterface $router,
        ParameterBagInterface $bag,
        SessionInterface $session,
        ProductFormatter $productFormatter,
        TagView $tagView,
        TagOptionsFormatter $tagOptionsFormatter,
        ShopPageRouterFormatter $shopPageRouterFormatter,
        ShopFilterFormatter $shopFilterFormatter
    ) {
        $this->router = $router;
        $this->bag = $bag;
        $this->session = $session;
        $this->productFormatter = $productFormatter;
        $this->tagView = $tagView;
        $this->tagOptionsFormatter = $tagOptionsFormatter;
        $this->shopPageRouterFormatter = $shopPageRouterFormatter;
        $this->shopFilterFormatter = $shopFilterFormatter;
    }

    public function formatResponse(
        array $data,
        string $locale,
        array $routeNames,
        ShopListRequestDto $shopListRequestDto,
        ShopPageOptionsDto $shopPageOptionsDto,
        array $filters,
        ?User $user = null
    ): array {
        $products = $this->productFormatter->getProducts($data['data'], $locale, $user);

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
