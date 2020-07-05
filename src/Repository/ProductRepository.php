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
use App\Entity\ProductHasTags;
use App\Entity\ProductSize;
use App\Entity\ProductTranslation;
use App\Entity\Tags;
use App\Entity\User;
use App\Entity\UserWishes;
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
     * @param DataTableModel $tableModel
     *
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData(DataTableModel $tableModel)
    {
        $query = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as total')
        ;

        if (!empty($tableModel->getSearch())) {
            $query
                ->innerJoin(ProductTranslation::class, 'pt', 'WITH', 'pt.product = p AND pt.locale = :locale')
                ->andWhere('
                pt.title LIKE :search or
                p.code LIKE :search
            ')
                ->setParameter('search', '%'.$tableModel->getSearch().'%')
                ->setParameter('locale', 'rs');
        }

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
                'p.code as code',
                'p.price as price',
                'p.status as status',
                'pt.title as title',
                'pt.slug',
                'p.showHomePage as show_home_page'
            )
            ->innerJoin(ProductTranslation::class, 'pt', 'WITH', 'pt.product = p AND pt.locale = :locale')
            ->setParameter('locale', 'rs')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->groupBy('pt.slug')
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        if (!empty($tableModel->getSearch())) {
            $query->andWhere('
                pt.title LIKE :search or
                p.code LIKE :search
            ')
                ->setParameter('search', '%'.$tableModel->getSearch().'%');
        }

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
     * @param User|null    $user
     * @param ParameterBag $searchData
     *
     * @return QueryBuilder
     */
    public function getDqlForPaginationPage(string $locale, ?User $user, ?ParameterBag $searchData): QueryBuilder
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
            if ($searchData->has('tags_localized')) {
                $tagsQuery = $this->_em->createQueryBuilder()
                    ->select('1')
                    ->from(ProductHasTags::class, 'pht')
                    ->leftJoin(Tags::class, 't', 'WITH', 'pht.tag = t.slug')
                    ->where('t.slug IN (:tagsSlug)')
                    ->andWhere('pht.product = p');

                $query->andWhere('EXISTS ('.$tagsQuery->getDQL().')')
                    ->setParameter('tagsSlug', $searchData->get('tags_localized'));
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

        if ($user !== null) {
            $wishQuery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from(UserWishes::class, 'uw')
                ->where('uw.user = :user')
                ->andWhere('uw.product = p');

            $query->addSelect(
                'IFELSE(EXISTS ('.$wishQuery->getDQL().'), 1, 0) as has_wish'
            )
                ->setParameter('user', $user);
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
    public function getRelatedProducts(string $locale, array $categories, Product $product, ?User $user): array
    {
        $searchParams = new ParameterBag(['categories' => $categories]);

        $query = $this->getDqlForPaginationPage($locale, $user, $searchParams)
            ->andWhere('p <> :product')
            ->setParameter('product', $product)
            ->setMaxResults(6);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string    $locale
     * @param User|null $user
     *
     * @return array
     */
    public function getForHomePage(string $locale, ?User $user): array
    {
        $query = $this->createQueryBuilder('p')
            ->select(
                'p.id',
                'pt.title',
                'pt.slug',
                'p.price',
                'p.discount',
                'i.name as image',
                'p.showHomePage as show_home_page'
            )
            ->innerJoin('p.productTranslations', 'pt')
            ->innerJoin('p.productHasImages', 'phi')
            ->innerJoin('phi.image', 'i')
            ->where('p.showHomePage > 0')
            ->andWhere('pt.locale = :locale')
            ->andWhere('i.isMain = :isMain')
            ->andWhere('p.status = :activeStatus')
            ->setParameter('locale', $locale)
            ->setParameter('isMain', true)
            ->setParameter('activeStatus', Product::STATUS_ACTIVE)
            ->groupBy('p.id');

        if ($user !== null) {
            $wishQuery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from(UserWishes::class, 'uw')
                ->where('uw.user = :user')
                ->andWhere('uw.product = p');

            $query->addSelect(
                'IFELSE(EXISTS ('.$wishQuery->getDQL().'), 1, 0) as has_wish'
            )
                ->setParameter('user', $user);
        }

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param Product $product
     *
     * @return Image
     * @throws NonUniqueResultException
     */
    public function getMainImage(Product $product): Image
    {
        $query = $this->createQueryBuilder('p')
            ->select(
                'image'
            )
            ->innerJoin('p.productHasImages', 'phi')
            ->innerJoin('phi.image', 'image')
            ->where('p = :product')
            ->andWhere('image.isMain = :isMain')
            ->setParameter('product', $product)
            ->setParameter('isMain', true);

        return $query->getQuery()->getOneOrNullResult();
    }
}
