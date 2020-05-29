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
                'pt.id',
                'pt.label as rs_name',
                'pt.mainSlug',
                'pten.label as en_name',
                'pt.slug'
            )
            ->innerJoin(Tags::class, 'pten', 'WITH', 'pten.mainSlug = pt.mainSlug AND pten.locale = \'en\'')
            ->where('pt.locale = \'rs\'')
            ->andWhere('pt.relatedType = :relatedType')
            ->setParameter('relatedType', $type)
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('pt.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

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
     * @param int $type
     *
     * @return array
     */
    public function getForOptions(int $type = Tags::TYPE_PRODUCT): array
    {
        $query = $this->createQueryBuilder('pt')
            ->select(
                'pt.mainSlug as value',
                'pt.label as title'
            )
            ->where('pt.locale = \'rs\'')
            ->andWhere('pt.relatedType = :relatedType')
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
        $query = $this->createQueryBuilder('pt')
            ->select(
                'p.id as productId',
                'pt.label'
            )
            ->innerJoin(ProductHasTags::class, 'pht', 'WITH', 'pht.tag = pt.mainSlug')
            ->innerJoin(Product::class, 'p', 'WITH', 'p = pht.product')
            ->where('pht.product IN (:products)')
            ->andWhere('pt.locale = :locale')
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
}
