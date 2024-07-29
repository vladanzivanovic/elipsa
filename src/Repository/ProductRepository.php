<?php

namespace App\Repository;

use App\Entity\CategoryTranslation;
use App\Entity\ColorTranslation;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\ProductHasCategories;
use App\Entity\ProductHasImages;
use App\Entity\ProductHasSizes;
use App\Entity\ProductHasTags;
use App\Entity\ProductOptions;
use App\Entity\ProductSize;
use App\Entity\ProductTranslation;
use App\Entity\Tags;
use App\Entity\TagTranslation;
use App\Entity\User;
use App\Entity\UserWishes;
use App\Model\DataTableModel;
use App\Request\Dto\ShopListRequestDto;
use App\Request\Dto\ShopPageOptionsDto;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ExtendedEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly array $shopOptions,
        private readonly string $defaultLocale,
        private readonly array $countries,
    ) {
        parent::__construct($registry, Product::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countData(DataTableModel $tableModel): mixed
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as total')
            ->innerJoin(ProductTranslation::class, 'pt', 'WITH', 'pt.product = p AND (pt.locale = :rsLocale OR pt.locale = :baLocale)')
            ->setParameter('rsLocale', 'rs')
            ->setParameter('baLocale', 'ba')
        ;

        $this->dataTableSearchPart($query, $tableModel);

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('p')
            ->select(
                'p.id',
                'p.code as code',
                'p.status as status',
                'pt.title as title',
                'pt.slug',
            )
            ->innerJoin(ProductTranslation::class, 'pt', 'WITH', 'pt.product = p AND (pt.locale = :rsLocale OR pt.locale = :baLocale)')
            ->setParameter('rsLocale', 'rs')
            ->setParameter('baLocale', 'ba')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->groupBy('p.id')
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        $this->dataTableSearchPart($query, $tableModel);

        return $query->getQuery()->getResult();
    }

    public function getLowestAndHighestPrice(): array
    {
        $query = $this->createQueryBuilder('p')
            ->select(
                'MIN (p.price) as lowPrice',
                'MAX (p.price) as highPrice'
            )
            ->where('p.status = :status')
            ->setParameter('status', Product::STATUS_ACTIVE);

        return $query->getQuery()->getArrayResult();
    }

    public function getDqlForPaginationPage(
        ShopListRequestDto $shopListRequestDto,
        string $countryCode,
        null|ShopPageOptionsDto $shopPageOptionsDto = null,
    ): QueryBuilder {
        $query = $this->createQueryBuilder('p')
            ->innerJoin(ProductOptions::class, 'po', 'WITH', 'po.product = p AND po.country = :country')
            ->where('p.status = :activeStatus')
            ->setParameter('activeStatus', Product::STATUS_ACTIVE, ParameterType::INTEGER)
            ->setParameter('country', $countryCode, ParameterType::STRING)
            ->groupBy('p.id')
            ->orderBy('p.id', 'DESC')
        ;

        if ($shopPageOptionsDto instanceof ShopPageOptionsDto && null !== $shopPageOptionsDto->sort) {
            $sort = $this->shopOptions['sort_mapping'][$shopPageOptionsDto->sort];

            $query->orderBy($sort[0], $sort[1]);
        }

        if ($shopListRequestDto->categories) {
            $this->createCategoriesQuery($query, $shopListRequestDto->categories);
        }

        if ($shopListRequestDto->attribute) {
            $this->createTagQueryForSearch(
                $query,
                $shopListRequestDto->attribute,
                Tags::PRODUCT_TYPE_ATTRIBUTE
            );
        }

        if ($shopListRequestDto->season) {
            $this->createTagQueryForSearch(
                $query,
                $shopListRequestDto->season,
                Tags::PRODUCT_TYPE_SEASON
            );
        }

        if ($shopListRequestDto->collection) {
            $this->createTagQueryForSearch(
                $query,
                $shopListRequestDto->collection,
                Tags::PRODUCT_TYPE_COLLECTION
            );
        }

        if ($shopListRequestDto->color) {
            $colorQuery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from(ProductHasImages::class, 'phiColor')
                ->innerJoin(ColorTranslation::class, 'colort', 'WITH', 'phiColor.color = colort.color')
                ->where('colort.slug IN (:colorSlugs)')
                ->andWhere('phiColor.product = p');

            $query->andWhere('EXISTS ('.$colorQuery->getDQL().')')
                ->setParameter('colorSlugs', $shopListRequestDto->color);
        }

        if ($shopListRequestDto->size) {
            $sizesQuery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from(ProductHasSizes::class, 'phs')
                ->innerJoin(ProductSize::class, 's', 'WITH', 's = phs.size')
                ->where('s.size IN (:sizes)')
                ->andWhere('phs.productOption = po');

            $query->andWhere('EXISTS ('.$sizesQuery->getDQL().')')
                ->setParameter('sizes', $shopListRequestDto->size);
        }

        if ($shopListRequestDto->price) {
            $price = $shopListRequestDto->price;

            $query->andWhere('po.price >= :lowPrice')
                ->andWhere('po.price <= :highPrice')
                ->setParameter('lowPrice', $price[0])
                ->setParameter('highPrice', $price[1]);
        }

        if($shopListRequestDto->search) {
            $query->innerJoin('p.productTranslations', 'pt');

            $this->createSearchString($query, $shopListRequestDto->search);
        }

        return $query;
    }

    
    public function getRelatedProducts(
        array $categories,
        Product $product,
        string $countryCode,
    ): array {
        $filterDto = new ShopListRequestDto();
        $filterDto->setCategories($categories);

        $query = $this->getDqlForPaginationPage($filterDto, $countryCode)
            ->andWhere('p <> :product')
            ->setParameter('product', $product)
            ->setMaxResults(6);

        return $query->getQuery()->getResult();
    }

    public function getForHomePage(
        string $homePagePosition,
        string $host
    ): array {
        $query = $this->createQueryBuilder('p')
            ->innerJoin(ProductOptions::class, 'po', 'WITH', 'po.product = p AND po.country = :country')
            ->andWhere('po.showHomePage LIKE :homePagePositionKey')
            ->andWhere('p.status = :activeStatus')
            ->setParameter('activeStatus', Product::STATUS_ACTIVE)
            ->setParameter('homePagePositionKey', '%"'.$homePagePosition.'":%')
            ->setParameter('country', $host)
            ->groupBy('p.id')
            ->orderBy('CAST(JSON_EXTRACT(po.showHomePage, \'$.'.$homePagePosition.'\') AS UNSIGNED)', 'ASC');

        return $query->getQuery()->getResult();
    }

    public function getProductsByQueryString(string $queryString): array
    {
        $query = $this->createQueryBuilder('p')
            ->innerJoin('p.productTranslations', 'pt');

        $this->createSearchString($query, $queryString);

        return $query->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    private function getSearchString(string $searchString): string
    {
        $searchLength = mb_strlen($searchString);
        $originalString = $searchString;

        if (4 > $searchLength) {
            return $searchString;
        }

        $splitString = explode(' ', $searchString);
        $searchArray = [];

        foreach ($splitString as $searchItem) {
            $itemLength = mb_strlen(trim($searchItem));

            if(0 === $itemLength) {
                continue;
            }

            if (1 < $itemLength) {
                $searchItem = mb_substr($searchItem, 0, $itemLength - 1);
            }

            $searchArray[] = '+'.$searchItem.'*';
        }

        $search = implode(',', $searchArray);

        try {
            $test = $this->_em->createQueryBuilder()
                ->select('COUNT (pt.id)')
                ->from(ProductTranslation::class, 'pt')
                ->where('(match(pt.title) against(:search BOOLEAN) > 0)')
                ->setParameter('search', $search);

            $count = $test->getQuery()->getSingleScalarResult();
        } catch (\Throwable $throwable) {
            return $originalString;
        }

        if ((int) $count === 0) {
            $originalString = mb_substr($originalString, 0, $searchLength - 1);

            $search = $this->getSearchString($originalString);
        }

        return $search;
    }

    public function dataTableSearchPart(QueryBuilder $query, DataTableModel $tableModel): void
    {
        $searchParams = $tableModel->getGeneralSearch();

        if (isset($searchParams['tags'])) {
            $this->createTagQueryForSearch($query, $searchParams['tags']);
        }

        if (isset($searchParams['categories'])) {
            $this->createCategoriesQuery($query, $searchParams['categories']);
        }

        if (isset($searchParams['title'])) {
            $this->createSearchString($query, $searchParams['title']);
        }

        if (isset($searchParams['code'])) {
            $query
                ->andWhere('p.code LIKE :searchCode')
                ->setParameter('searchCode', $searchParams['code']);
        }

        if (isset($searchParams['sold']) || isset($searchParams['home_page_show'])) {
            $countries = [];

            $query
                ->innerJoin(ProductOptions::class, 'po', 'WITH', 'po.product = p AND po.country IN (:countries)');

            foreach ($this->countries as $countryCode => $country) {
                $countries[] = $countryCode;
            }

            $query->setParameter('countries', $countries);
        }

        if (isset($searchParams['sold'])) {
            $query
                ->andWhere('po.sold = :isSold')
                ->setParameter('isSold', true);
        }

        if (isset($searchParams['home_page_show'])) {
            if ('all' === $searchParams['home_page_show']) {
                $query
                    ->andWhere('(po.showHomePage LIKE :upShowHomePage OR po.showHomePage LIKE :downShowHomePage)')
                    ->setParameter('upShowHomePage', '%"'.ProductOptions::HOME_PAGE_UP.'":%')
                    ->setParameter('downShowHomePage', '%"'.ProductOptions::HOME_PAGE_DOWN.'":%');
            } else {
                $query
                    ->andWhere('po.showHomePage LIKE :showHomePage')
                    ->setParameter('showHomePage', '{"'.$searchParams['home_page_show'].'":%');
            }
        }

        $statuses = [Product::STATUS_ACTIVE, Product::STATUS_PENDING];

        if (isset($searchParams['product_status'])) {
            $statuses = explode(',', $searchParams['product_status']);
        }

        if (isset($searchParams['country'])) {
            $query->andWhere('p.availableCountries LIKE :country')
                ->setParameter('country', '%'.$searchParams['country'].'%');
        }

        $query->andWhere('p.status IN (:statuses)')
            ->setParameter('statuses', $statuses);
    }

    private function createTagQueryForSearch(
        QueryBuilder $query,
        array $tags,
        string $productType = null
    ): void {
        $tagQuery = $this->_em->createQueryBuilder()
            ->select('1')
            ->from(TagTranslation::class, 'tt')
            ->innerJoin('tt.tag', 't')
            ->innerJoin(ProductHasTags::class, 'pht', 'WITH', 'pht.tag = tt.tag')
            ->where('tt.slug IN (:tagsSlug)')
            ->andWhere('pht.product = p');

        if (null !== $productType) {
            $tagQuery->andWhere('t.productType = :productType');

            $query->setParameter('productType', $productType);
        }

        $query->andWhere('EXISTS ('.$tagQuery->getDQL().')')
            ->setParameter('tagsSlug', $tags);
    }

    private function createCategoriesQuery(QueryBuilder $query, array $categories): void
    {
        $categoryQuery = $this->_em->createQueryBuilder()
            ->select('1')
            ->from(ProductHasCategories::class, 'phc')
            ->leftJoin(CategoryTranslation::class, 'ct', 'WITH', 'phc.category = ct.category')
            ->where('ct.slug IN (:categorySlugs)')
            ->andWhere('phc.product = p');

        $query->andWhere('EXISTS (' . $categoryQuery->getDQL() . ')')
            ->setParameter('categorySlugs', $categories);
    }

    private function createSearchString(QueryBuilder $query, string $searchString): void
    {
        $search = $this->getSearchString($searchString);

        $query
            ->andWhere('
                (match(pt.title) against(:searchTitle BOOLEAN) > 0)
            ')
            ->setParameter('searchTitle', $search);
    }
}
