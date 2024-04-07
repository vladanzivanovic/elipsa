<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\User;
use App\Repository\ProductRepository;
use App\Request\Dto\ShopListRequestDto;
use App\Request\Dto\ShopPageOptionsDto;
use App\Services\PaginationService;
use App\ShopTrait;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShopPageCollector
{
    use ShopTrait;

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly PaginationService $paginationService,
        private readonly TranslatorInterface $translator,
        private readonly ParameterBagInterface $bag,
    ) {}

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

        return $this->paginationService->pagination($productDql, $shopPageOptionsDto->page, $shopPageOptionsDto->limit);
    }
}
