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
        string $locale,
        ShopListRequestDto $shopListRequestDto,
        ShopPageOptionsDto $shopPageOptionsDto,
        ?UserInterface $user,
        bool $isTrendyPage = false
    ): array {
        $sizes = $this->sizeRepository->getForOptions();
        $prices = $this->productRepository->getLowestAndHighestPrice();

        $filters = [
            'sizes'     => $sizes,
            'colors' => $this->colorRepository->getByLocale($locale),
            'collection' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_COLLECTION),
            'season' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_SEASON),
            'attributes' => $this->tagsRepository->getByProductType(Tags::PRODUCT_TYPE_ATTRIBUTE),
            'prices'    => $prices[0],
        ];

        return $this->collectForApi($shopListRequestDto, $shopPageOptionsDto, $user, $isTrendyPage);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function collectForApi(
        ShopListRequestDto $shopListRequestDto,
        ShopPageOptionsDto $shopPageOptionsDto,
        ?User $user,
        bool $isTrendyPage = false
    ): array {
        $searchCriteria = null;
        $localizedUrl = null;

//        if (null !== $searchData) {
//            $searchCriteria = $this->parseSearchData($searchData);
//            $localizedUrl = $this->shopPageRouterFormatter->createUrlString($searchCriteria, $locale === 'rs' ? 'en' : 'rs');
//        }
//
//        if (null !== $searchCriteria && $searchCriteria->has('tags')) {
//            $searchCriteria->set('tags_localized', $searchCriteria->get('tags'));
//
//            if ($locale !== 'rs') {
//                $tags = $this->tagsRepository->getArrayForLocalization($searchCriteria->get('tags'), $locale);
//                $searchCriteria->set('tags_localized', array_column($tags, 'mainSlug'));
//            }
//        }
//
//        $limit = $shopFilterDto->limit ?? 12;

        $productDql = $this->productRepository->getDqlForPaginationPage($shopListRequestDto, $shopPageOptionsDto, $user);
        $data = $this->paginationService->pagination($productDql, $shopPageOptionsDto->page, $shopPageOptionsDto->limit);

//        if (null !== $searchData && $searchCriteria->has('tags_localized')) {
//            $searchCriteria->remove('tags_localized');
//        }

//        $collection = [
//            'products'          => $products,
//            'search_criteria'   => null !== $searchData ? $searchCriteria : null,
//            'localized_url'     => $localizedUrl,
//        ];

//        if (true === $isTrendyPage) {
//            $collection['tags'] = $this->tagsRepository->findBy(['relatedType' => Tags::TYPE_PRODUCT, 'productType' => Tags::PRODUCT_TYPE_SEASON]);
//        }

        return $data;
    }
}
