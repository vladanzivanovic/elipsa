<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Product;
use App\Entity\Tags;
use App\Entity\User;
use App\Formatter\Site\Router\ShopPageRouterFormatter;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use App\Request\Dto\ShopListRequestDto;
use App\Request\Dto\ShopPageOptionsDto;
use App\Services\PaginationService;
use App\ShopTrait;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShopPageCollector
{
    use ShopTrait;

    private ProductColorRepository $colorRepository;

    private ProductSizeRepository $sizeRepository;

    private ProductRepository $productRepository;

    private PaginationService $paginationService;

    private TagsRepository $tagsRepository;

    private TranslatorInterface $translator;

    private ParameterBagInterface $bag;

    private ShopPageRouterFormatter $shopPageRouterFormatter;

    private SessionInterface $session;

    public function __construct(
        ProductColorRepository $colorRepository,
        ProductSizeRepository $sizeRepository,
        ProductRepository $productRepository,
        PaginationService $paginationService,
        TagsRepository $tagsRepository,
        TranslatorInterface $translator,
        ParameterBagInterface $bag,
        ShopPageRouterFormatter $shopPageRouterFormatter,
        SessionInterface $session
    ) {
        $this->colorRepository = $colorRepository;
        $this->sizeRepository = $sizeRepository;
        $this->productRepository = $productRepository;
        $this->paginationService = $paginationService;
        $this->tagsRepository = $tagsRepository;
        $this->translator = $translator;
        $this->bag = $bag;
        $this->shopPageRouterFormatter = $shopPageRouterFormatter;
        $this->session = $session;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function collect(
        ShopListRequestDto $shopListRequestDto,
        ShopPageOptionsDto $shopPageOptionsDto,
        ?UserInterface $user
    ): array {
        return $this->collectForApi($shopListRequestDto, $shopPageOptionsDto, $user);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function collectForApi(
        ShopListRequestDto $shopListRequestDto,
        ShopPageOptionsDto $shopPageOptionsDto,
        ?User $user
    ): array {
        $productDql = $this->productRepository->getDqlForPaginationPage($shopListRequestDto, $shopPageOptionsDto, $user);
        $data = $this->paginationService->pagination($productDql, $shopPageOptionsDto->page, $shopPageOptionsDto->limit);

        return $data;
    }
}
