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
use Doctrine\Persistence\ManagerRegistry;
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
            $colorQuery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from(ProductHasImages::class, 'phiColor')
                ->innerJoin(ColorTranslation::class, 'colort', 'WITH', 'phiColor.color = colort.color AND colort.locale = :locale')
                ->where('REGEXP(colort.slug, :regex) = true')
                ->andWhere('phiColor.product = p');

            $tagsQuery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from(ProductHasTags::class, 'pht')
                ->leftJoin(Tags::class, 't', 'WITH', 'pht.tag = t.slug')
                ->where('REGEXP(t.slug, :regex) = true')
                ->andWhere('pht.product = p');

            $categoryQuery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from(ProductHasCategories::class, 'phc')
                ->leftJoin(CategoryTranslation::class, 'ct', 'WITH', 'phc.category = ct.category AND ct.locale = :locale')
                ->where('REGEXP(ct.slug, :regex) = true')
                ->andWhere('phc.product = p');

            $query
                ->innerJoin(ProductTranslation::class, 'pt', 'WITH', 'pt.product = p AND pt.locale = :locale')
                ->andWhere('
                pt.title LIKE :search or
                p.code LIKE :search or
                EXISTS ('.$colorQuery->getDQL().') or
                EXISTS ('.$tagsQuery->getDQL().') or 
                EXISTS ('.$categoryQuery->getDQL().')
            ')
                ->setParameter('search', '%'.$tableModel->getSearch().'%')
                ->setParameter('regex', $tableModel->getSearch())
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
                'p.id',
                'p.code as code',
                'p.price as price',
                'p.discount as discount',
                'p.status as status',
                'pt.title as title',
                'pt.slug',
                'p.showHomePage as show_home_page',
                'GROUP_CONCAT(s.size ORDER BY s.size ASC SEPARATOR \', \') as sizes'
            )
            ->innerJoin(ProductTranslation::class, 'pt', 'WITH', 'pt.product = p AND pt.locale = :locale')
            ->innerJoin('p.productHasSizes', 'ps')
            ->innerJoin('ps.size', 's')
            ->setParameter('locale', 'rs')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->groupBy('pt.slug')
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        if (!empty($tableModel->getSearch())) {
            $colorQuery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from(ProductHasImages::class, 'phiColor')
                ->innerJoin(ColorTranslation::class, 'colort', 'WITH', 'phiColor.color = colort.color AND colort.locale = :locale')
                ->where('REGEXP(colort.slug, :regex) = true')
                ->andWhere('phiColor.product = p');

            $tagsQuery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from(ProductHasTags::class, 'pht')
                ->leftJoin(Tags::class, 't', 'WITH', 'pht.tag = t.slug')
                ->where('REGEXP(t.slug, :regex) = true')
                ->andWhere('pht.product = p');

            $categoryQuery = $this->_em->createQueryBuilder()
                ->select('1')
                ->from(ProductHasCategories::class, 'phc')
                ->leftJoin(CategoryTranslation::class, 'ct', 'WITH', 'phc.category = ct.category AND ct.locale = :locale')
                ->where('REGEXP(ct.slug, :regex) = true')
                ->andWhere('phc.product = p');

            $query->andWhere('
                pt.title LIKE :search or
                p.code LIKE :search or
                EXISTS ('.$colorQuery->getDQL().') or 
                EXISTS ('.$tagsQuery->getDQL().') or 
                EXISTS ('.$categoryQuery->getDQL().')
            ')
                ->setParameter('search', '%'.$tableModel->getSearch().'%')
                ->setParameter('regex', $tableModel->getSearch());
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

    public function getDqlForPaginationPage(?User $user, ?ParameterBag $searchData): QueryBuilder
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.status = :activeStatus')
            ->setParameter('activeStatus', Product::STATUS_ACTIVE, ParameterType::INTEGER)
            ->groupBy('p.id')
            ->orderBy('p.id', 'DESC')
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

            if($searchData->has('search')) {
                $searchWords = explode(' ', $searchData->get('search')[0]);

                array_walk($searchWords, function (&$word) {
                    $subWord = substr($word, 0, strlen($word) - 1);

                    $word = '+'.str_replace($subWord, $subWord.'*', $word);
                });

                $search = implode(' ', $searchWords);

                $categorySearchQuery = $this->_em->createQueryBuilder()
                    ->select('1')
                    ->from(ProductHasCategories::class, 'phc1')
                    ->innerJoin(CategoryTranslation::class, 'ct1', 'WITH', 'phc1.category = ct1.category')
                    ->where('match(ct1.title) against(:search BOOLEAN) > 0')
                    ->andWhere('phc1.product = p');

                $tagsSearchQuery = $this->_em->createQueryBuilder()
                    ->select('1')
                    ->from(ProductHasTags::class, 'pht2')
                    ->leftJoin(Tags::class, 't2', 'WITH', 'pht2.tag = t2.slug')
                    ->where('match(t2.label) against(:search BOOLEAN) > 0')
                    ->andWhere('pht2.product = p');

                $query->andWhere('
                    (EXISTS ('.$categorySearchQuery->getDQL().')) OR 
                    (match(pt.title, pt.description, pt.cleaning) against(:search BOOLEAN) > 1) OR
                    (EXISTS ('.$tagsSearchQuery->getDQL().'))
                ')
                    ->setParameter('search', $search);
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
    public function getRelatedProducts(array $categories, Product $product, ?User $user): array
    {
        $searchParams = new ParameterBag(['categories' => $categories]);

        $query = $this->getDqlForPaginationPage($user, $searchParams)
            ->andWhere('p <> :product')
            ->setParameter('product', $product)
            ->setMaxResults(6);

        return $query->getQuery()->getResult();
    }

    /**
     * @param string    $locale
     * @param User|null $user
     *
     * @return array
     */
    public function getForHomePage(?User $user): array
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.showHomePage > 0')
            ->andWhere('p.status = :activeStatus')
            ->setParameter('activeStatus', Product::STATUS_ACTIVE)
            ->groupBy('p.id')
            ->orderBy('RAND()');

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

        return $query->getQuery()->getResult();
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
