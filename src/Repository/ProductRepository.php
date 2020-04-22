<?php

namespace App\Repository;

use App\Entity\CategoryTranslation;
use App\Entity\ColorTranslation;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ProductHasCategories;
use App\Entity\ProductHasImages;
use App\Entity\ProductHasSizes;
use App\Entity\ProductSize;
use App\Entity\ProductTranslation;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ExtendedEntityRepository
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * ProductRepository constructor.
     *
     * @param ManagerRegistry  $registry
     * @param SessionInterface $session
     */
    public function __construct(ManagerRegistry $registry, SessionInterface $session)
    {
        parent::__construct($registry, Product::class);
        $this->session = $session;
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('product')
            ->select('COUNT(product.id) as total')
        ;

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param DataTableModel $tableModel
     *
     * @return array
     */
    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('p')
            ->select(
                'p.id',
                'p.price',
                'p.status',
                'pt.title',
                'pt.slug',
                'p.showHomePage as show_home_page'
            )
            ->innerJoin(ProductTranslation::class, 'pt', 'WITH', 'pt.product = p AND pt.locale = :locale')
            ->setParameter('locale', 'rs')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->groupBy('pt.slug')
            ->orderBy('p.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
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

    /**
     * @param string       $locale
     * @param ParameterBag $searchData
     *
     * @return QueryBuilder
     */
    public function getDqlForPaginationPage(string $locale, ?ParameterBag $searchData): QueryBuilder
    {
        $query = $this->createQueryBuilder('p')
            ->select(
                'p.id',
                'p.price',
                'p.discount',
                'pt.title',
                'pt.slug',
                'pt.shortDescription as short_description',
                'i.name as image'
            )
            ->innerJoin('p.productTranslations', 'pt')
            ->innerJoin('p.productHasImages', 'phi')
            ->innerJoin('phi.image', 'i')
            ->where('p.status = :activeStatus')
            ->andWhere('pt.locale = :locale')
            ->andWhere('i.isMain = :isMain')
            ->setParameter('activeStatus', Product::STATUS_ACTIVE, ParameterType::INTEGER)
            ->setParameter('locale', $locale, ParameterType::STRING)
            ->setParameter('isMain', true, ParameterType::BOOLEAN)
            ->groupBy('p.id')
        ;

        if (null !== $searchData && $searchData->has('sort')) {
            $sort = $searchData->get('sort');

            $query->orderBy($sort[0], $sort[1]);
        }

        if (null !== $searchData) {
            if ($searchData->has('categories')) {
                $categoryQuery = $this->_em->createQueryBuilder()
                    ->select('1')
                    ->from(ProductHasCategories::class, 'phc')
                    ->leftJoin(CategoryTranslation::class, 'ct', 'WITH', 'phc.category = ct.category')
                    ->where('ct.slug IN (:categorySlugs)')
                    ->andWhere('phc.product = p');

                $query->andWhere('EXISTS ('.$categoryQuery->getDQL().')')
                    ->setParameter('categorySlugs', $searchData->get('categories'));
            }

            if ($searchData->has('color')) {
                $colorQuery = $this->_em->createQueryBuilder()
                    ->select('1')
                    ->from(ProductHasImages::class, 'phiColor')
                    ->innerJoin(ColorTranslation::class, 'colort', 'WITH', 'phiColor.color = colort.color')
                    ->where('colort.slug IN (:colorSlugs)')
                    ->andWhere('phiColor.product = p');

                $query->andWhere('EXISTS ('.$colorQuery->getDQL().')')
                    ->setParameter('colorSlugs', $searchData->get('color'));
            }

            if ($searchData->has('size')) {
                $sizesQuery = $this->_em->createQueryBuilder()
                    ->select('1')
                    ->from(ProductHasSizes::class, 'phs')
                    ->innerJoin(ProductSize::class, 's', 'WITH', 's = phs.size')
                    ->where('s.size IN (:sizes)')
                    ->andWhere('phs.product = p');

                $query->andWhere('EXISTS ('.$sizesQuery->getDQL().')')
                    ->setParameter('sizes', $searchData->get('size'));
            }

            if ($searchData->has('price')) {
                $price = explode('-', $searchData->get('price')[0]);

                $query->andWhere('p.price >= :lowPrice')
                    ->andWhere('p.price <= :highPrice')
                    ->setParameter('lowPrice', $price[0])
                    ->setParameter('highPrice', $price[1]);
            }
        }

        return $query;
    }

    /**
     * @param string  $locale
     * @param array   $categories
     * @param Product $product
     *
     * @return array
     */
    public function getRelatedProducts(string $locale, array $categories, Product $product): array
    {
        $searchParams = new ParameterBag(['categories' => $categories]);

        $query = $this->getDqlForPaginationPage($locale, $searchParams)
            ->andWhere('p <> :product')
            ->setParameter('product', $product)
            ->setMaxResults(6);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param array  $categories
     * @param string $locale
     *
     * @return array
     */
    public function getForHomePage(array $categories, string $locale): array
    {
        $query = $this->createQueryBuilder('p')
            ->select(
                'p.id',
                'pt.title',
                'pt.slug',
                'p.price',
                'p.discount',
                'i.name as image',
                'GROUP_CONCAT(IDENTITY(phc.category)) as categories'
            )
            ->innerJoin('p.productTranslations', 'pt')
            ->innerJoin('p.productHasCategories', 'phc')
            ->innerJoin('p.productHasImages', 'phi')
            ->innerJoin('phi.image', 'i')
            ->where('p.showHomePage = :showHomePage')
            ->andWhere('pt.locale = :locale')
            ->andWhere('phc.category IN (:categories)')
            ->andWhere('i.isMain = :isMain')
            ->andWhere('p.status = :activeStatus')
            ->setParameter('showHomePage', true)
            ->setParameter('locale', $locale)
            ->setParameter('categories', $categories)
            ->setParameter('isMain', true)
            ->setParameter('activeStatus', Product::STATUS_ACTIVE)
            ->groupBy('p.id');

        return $query->getQuery()->getArrayResult();
    }
}
