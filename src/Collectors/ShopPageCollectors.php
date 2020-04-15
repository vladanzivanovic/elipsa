<?php

declare(strict_types=1);

namespace App\Collectors;

use App\Repository\CategoryRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\ProductTagsRepository;
use App\Services\PaginationService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShopPageCollectors
{
    /**
     * @var ProductColorRepository
     */
    private $colorRepository;

    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var PaginationService
     */
    private $paginationService;
    /**
     * @var ProductTagsRepository
     */
    private $tagsRepository;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param ProductColorRepository $colorRepository
     * @param ProductSizeRepository  $sizeRepository
     * @param ProductRepository      $productRepository
     * @param PaginationService      $paginationService
     * @param ProductTagsRepository  $tagsRepository
     * @param TranslatorInterface    $translator
     * @param ParameterBagInterface  $bag
     */
    public function __construct(
        ProductColorRepository $colorRepository,
        ProductSizeRepository $sizeRepository,
        ProductRepository $productRepository,
        PaginationService $paginationService,
        ProductTagsRepository $tagsRepository,
        TranslatorInterface $translator,
        ParameterBagInterface $bag
    ) {
        $this->colorRepository = $colorRepository;
        $this->sizeRepository = $sizeRepository;
        $this->productRepository = $productRepository;
        $this->paginationService = $paginationService;
        $this->tagsRepository = $tagsRepository;
        $this->translator = $translator;
        $this->bag = $bag;
    }

    public function collect(string $locale, int $currentPage, ?string $searchData = null)
    {
        $colors = $this->colorRepository->getByLocale($locale);
        $sizes = $this->sizeRepository->getForOptions();
        $prices = $this->productRepository->getLowestAndHighestPrice();

        $data = [
            'colors'    => $colors,
            'sizes'     => $sizes,
            'prices'    => $prices[0],
        ];

        return $data + $this->collectForApi($locale, $currentPage, $searchData);
    }

    /**
     * @param string $locale
     * @param int    $currentPage
     * @param string $searchData
     *
     * @return array
     */
    public function collectForApi(string $locale, int $currentPage, ?string $searchData = null): array
    {
        $searchCriteria = null;

        if (null !== $searchData) {
            $searchCriteria = $this->parseSearchData($searchData);
        }

        $limit = null !== $searchCriteria && $searchCriteria->has('limit') ? (int) $searchCriteria->get('limit')[0] : 12;

        $productDql = $this->productRepository->getDqlForPaginationPage($locale, $searchCriteria);
        $products = $this->paginationService->pagination($productDql, $currentPage, $limit);

        $productIds = array_column($products['data'], 'id');

        $productColors = $this->colorRepository->getByProducts($productIds, $locale);
        $productSizes = $this->sizeRepository->getByProducts($productIds);
        $productTags = $this->tagsRepository->getByProducts($productIds, $locale);

        return [
            'products'          => $products,
            'product_colors'    => $productColors,
            'product_sizes'     => $productSizes,
            'product_tags'      => $productTags,
            'search_criteria'   => null !== $searchData ? $searchCriteria : null,
        ];
    }

    /**
     * @param string $searchData
     *
     * @return ParameterBag
     */
    private function parseSearchData(string $searchData): ParameterBag
    {
        $searchArray = explode('/', $searchData);
        $filters = [];
        $criteria = [];
        $sortMapper = $this->bag->get('shop')['sort_mapping'];

        for ($i = 0; $i < count($searchArray); $i++) {
            if ($i % 2 == 0) {
                $filters[] = $this->translator->trans($searchArray[$i], [], 'messages', 'en');

                continue;
            }

            $value = explode('+', $searchArray[$i]);

            if (end($filters) === 'sort') {
                $value = $sortMapper[$value[0]];
            }

            $criteria[] = $value;
        }

        return new ParameterBag(array_combine($filters, $criteria));
    }
}