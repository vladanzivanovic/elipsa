<?php

namespace App\Repository;

use App\Entity\Blog;
use App\Entity\BlogHasTags;
use App\Entity\Product;
use App\Entity\ProductHasTags;
use App\Entity\Tags;
use App\Model\DataTableModel;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Tags|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tags|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tags[]    findAll()
 * @method Tags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagsRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tags::class);
    }

    /**
     * @param int $type
     *
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData(int $type = Tags::TYPE_PRODUCT)
    {
        $query = $this->createQueryBuilder('pt')
            ->select('COUNT(pt.id) as total')
            ->where('pt.locale = \'rs\'')
            ->andWhere('pt.relatedType = :relatedType')
            ->setParameter('relatedType', $type)
        ;

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param DataTableModel $tableModel
     * @param int            $type
     *
     * @return array
     */
    public function getAdminList(DataTableModel $tableModel, int $type = Tags::TYPE_PRODUCT): array
    {
        $query = $this->createQueryBuilder('pt')
            ->select(
                'pt.id as id',
                'pt.label as rs_name',
                'pt.mainSlug as minSlug',
                'pten.label as en_name',
                'pt.slug as slug'
            )
            ->innerJoin(Tags::class, 'pten', 'WITH', 'pten.mainSlug = pt.mainSlug AND pten.locale = \'en\'')
            ->where('pt.locale = \'rs\'')
            ->andWhere('pt.relatedType = :relatedType')
            ->setParameter('relatedType', $type)
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
            ->groupBy('pt.id')
        ;

        if ($type === Tags::TYPE_BLOG) {
            $query->addSelect('COUNT(bht.id) as total_products')
                ->leftJoin(BlogHasTags::class, 'bht', 'WITH', 'bht.tag = pt.mainSlug');
        }

        if ($type === Tags::TYPE_PRODUCT) {
            $query->addSelect('COUNT(pht.id) as total_products')
                ->leftJoin(ProductHasTags::class, 'pht', 'WITH', 'pht.tag = pt.mainSlug');
        }

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $mainSlug
     * @param array  $locales
     * @param int    $type
     *
     * @return array
     */
    public function getByMainSlugAndLocales(string $mainSlug, array $locales, int $type): array
    {
        $query = $this->createQueryBuilder('pt')
            ->select(
                'pt.label',
                'pt.locale'
            )
            ->where('pt.mainSlug = :mainSlug')
            ->andWhere('pt.locale IN (:locales)')
            ->andWhere('pt.relatedType = :relatedType')
            ->setParameter('relatedType', $type)
            ->setParameter('mainSlug', $mainSlug)
            ->setParameter('locales', $locales);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $mainSlug
     *
     * @return void
     */
    public function remove(string $mainSlug): void
    {
        $query = $this->createQueryBuilder('pt')
            ->delete()
            ->where('pt.mainSlug = :mainSlug')
            ->setParameter('mainSlug', $mainSlug);

        $query->getQuery()->execute();
    }

    /**
     * @param string $locale
     *
     * @param int    $type
     *
     * @return array
     */
    public function getForNavigationMenu(string $locale, int $type = Tags::TYPE_PRODUCT): array
    {
        $query = $this->createQueryBuilder('t')
            ->select(
                't.id',
                't.label',
                't.slug'
            )
            ->where('t.locale = :locale')
            ->andWhere('t.relatedType = :relatedType')
            ->setParameter('locale', $locale)
            ->setParameter('relatedType', $type);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param int    $type
     * @param string $locale
     *
     * @return array
     */
    public function getForOptions(int $type = Tags::TYPE_PRODUCT, string $locale = 'rs'): array
    {
        $query = $this->createQueryBuilder('t')
            ->select(
                't.mainSlug as value',
                't.slug',
                't.label as title'
            )
            ->where('t.locale = :locale')
            ->andWhere('t.relatedType = :relatedType')
            ->setParameter('locale', $locale)
            ->setParameter('relatedType', $type);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getByProduct(Product $product): array
    {
        $query = $this->createQueryBuilder('pt')
            ->select(
                'pt.mainSlug'
            )
            ->innerJoin(ProductHasTags::class, 'pht', 'WITH', 'pht.tag = pt.mainSlug AND pht.product = :product')
            ->where('pt.locale = \'rs\'')
            ->setParameter('product', $product);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param Blog   $blog
     *
     * @param string $locale
     *
     * @return array
     */
    public function getByBlog(Blog $blog, string $locale = 'rs'): array
    {
        $query = $this->createQueryBuilder('t')
            ->select(
                't.mainSlug',
                't.slug',
                't.label'
            )
            ->innerJoin(BlogHasTags::class, 'bht', 'WITH', 'bht.tag = t.mainSlug AND bht.blog = :blog')
            ->where('t.locale = :locale')
            ->setParameter('locale', $locale)
            ->setParameter('blog', $blog);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param array  $products
     *
     * @param string $locale
     *
     * @return array
     */
    public function getByProducts(array $products, string $locale): array
    {
        $query = $this->createQueryBuilder('t')
            ->select(
                'p.id as productId',
                't.slug',
                't.label'
            )
            ->innerJoin(ProductHasTags::class, 'pht', 'WITH', 'pht.tag = t.mainSlug')
            ->innerJoin(Product::class, 'p', 'WITH', 'p = pht.product')
            ->where('pht.product IN (:products)')
            ->andWhere('t.locale = :locale')
            ->setParameter('products', $products)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param array  $blogList
     * @param string $locale
     *
     * @return array
     */
    public function getByBlogList(array $blogList, string $locale): array
    {
        $query = $this->createQueryBuilder('t')
            ->select(
                'b.id as blogId',
                't.label'
            )
            ->innerJoin(BlogHasTags::class, 'bht', 'WITH', 'bht.tag = t.mainSlug')
            ->innerJoin(Blog::class, 'b', 'WITH', 'b = bht.blog')
            ->where('bht.blog IN (:blogList)')
            ->andWhere('t.locale = :locale')
            ->setParameter('blogList', $blogList)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getForLocalization(string $slug, string $locale)
    {
        $query = $this->createQueryBuilder('t')
            ->select(
                'tt.slug'
            )
            ->innerJoin(Tags::class, 'tt', 'WITH', 'tt.mainSlug = t.mainSlug')
            ->where('t.slug = :slug')
            ->andWhere('tt.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param array  $slugs
     * @param string $locale
     *
     * @return int|mixed|string
     */
    public function getArrayForLocalization(array $slugs, string $locale)
    {
        $query = $this->createQueryBuilder('t')
            ->select(
                'tt.mainSlug'
            )
            ->innerJoin(Tags::class, 'tt', 'WITH', 'tt.mainSlug = t.mainSlug')
            ->where('t.slug IN (:slug)')
            ->andWhere('tt.locale = :locale')
            ->setParameter('slug', $slugs)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @param int    $relatedType
     *
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getMainSlug(string $slug, string $locale, int $relatedType)
    {
        $query = $this->createQueryBuilder('t')
            ->select(
                't.mainSlug'
            )
            ->where('t.slug = :slug')
            ->andWhere('t.locale = :locale')
            ->andWhere('t.relatedType = :relatedType')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale)
            ->setParameter('relatedType', $relatedType);

        return $query->getQuery()->getArrayResult();
    }
}
