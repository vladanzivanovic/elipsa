<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Tags;
use App\Repository\BlogRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use App\Services\PaginationService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Contracts\Translation\TranslatorInterface;

final class BlogListPageCollector
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
     * @var TagsRepository
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
     * @var BlogRepository
     */
    private $blogRepository;

    /**
     * @param ProductColorRepository $colorRepository
     * @param ProductSizeRepository  $sizeRepository
     * @param ProductRepository      $productRepository
     * @param PaginationService      $paginationService
     * @param TagsRepository         $tagsRepository
     * @param TranslatorInterface    $translator
     * @param ParameterBagInterface  $bag
     * @param BlogRepository         $blogRepository
     */
    public function __construct(
        ProductColorRepository $colorRepository,
        ProductSizeRepository $sizeRepository,
        ProductRepository $productRepository,
        PaginationService $paginationService,
        TagsRepository $tagsRepository,
        TranslatorInterface $translator,
        ParameterBagInterface $bag,
        BlogRepository $blogRepository
    ) {
        $this->colorRepository = $colorRepository;
        $this->sizeRepository = $sizeRepository;
        $this->productRepository = $productRepository;
        $this->paginationService = $paginationService;
        $this->tagsRepository = $tagsRepository;
        $this->translator = $translator;
        $this->bag = $bag;
        $this->blogRepository = $blogRepository;
    }

    /**
     * @param string $locale
     * @param int    $currentPage
     * @param string $searchData
     *
     * @return array
     */
    public function collect(string $locale, int $currentPage, ?string $searchData = null): array
    {

        $blogDql = $this->blogRepository->getDqlForPaginationPage($locale);
        $blogList = $this->paginationService->pagination($blogDql, $currentPage, 12);

        $blogIds = array_column($blogList['data'], 'id');

        $tags = $this->tagsRepository->findBy(['relatedType' => Tags::TYPE_BLOG, 'locale' => $locale]);

        return [
            'blog_list'         => $blogList,
            'tags'              => $tags,
            'search_criteria'   => null !== $searchData ? $searchCriteria : null,
        ];
    }

//    /**
//     * @param string $searchData
//     *
//     * @return ParameterBag
//     */
//    private function parseSearchData(string $searchData): ParameterBag
//    {
//        $searchArray = explode('/', $searchData);
//        $filters = [];
//        $criteria = [];
//        $sortMapper = $this->bag->get('shop')['sort_mapping'];
//
//        for ($i = 0; $i < count($searchArray); $i++) {
//            if ($i % 2 == 0) {
//                $filters[] = $this->translator->trans($searchArray[$i], [], 'messages', 'en');
//
//                continue;
//            }
//
//            $value = explode('+', $searchArray[$i]);
//
//            if (end($filters) === 'sort') {
//                $value = $sortMapper[$value[0]];
//            }
//
//            $criteria[] = $value;
//        }
//
//        return new ParameterBag(array_combine($filters, $criteria));
//    }
}