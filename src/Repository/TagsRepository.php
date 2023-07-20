<?php

namespace App\Repository;

use App\Entity\Blog;
use App\Entity\BlogHasTags;
use App\Entity\Product;
use App\Entity\ProductHasTags;
use App\Entity\Tags;
use App\Entity\TagTranslation;
use App\Model\DataTableModel;
use Doctrine\Persistence\ManagerRegistry;
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
        $query = $this->createQueryBuilder('t')
            ->select('COUNT(t.id) as total')
            ->where('t.relatedType = :relatedType')
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
        $query = $this->createQueryBuilder('t')
            ->select(
                't.id as id',
                'tt.title as rs_name',
                'tt.slug as slug',
                't.productType as product_type'
            )
            ->innerJoin(TagTranslation::class, 'tt', 'WITH', 'tt.tag = t.id')
            ->where('tt.locale = \'rs\'')
            ->andWhere('t.relatedType = :relatedType')
            ->setParameter('relatedType', $type)
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
            ->groupBy('tt.id')
        ;

        if ($type === Tags::TYPE_BLOG) {
            $query->addSelect('COUNT(bht.id) as total_products')
                ->leftJoin(BlogHasTags::class, 'bht', 'WITH', 'bht.tag = t.id');
        }

        if ($type === Tags::TYPE_PRODUCT) {
            $query->addSelect('COUNT(pht.id) as total_products')
                ->leftJoin(ProductHasTags::class, 'pht', 'WITH', 'pht.tag = t.id');
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
    public function getByTranslation(string $mainSlug, array $locales, int $type): array
    {
        $query = $this->createQueryBuilder('t')
            ->where('t.mainSlug = :mainSlug')
            ->andWhere('t.locale IN (:locales)')
            ->andWhere('t.relatedType = :relatedType')
            ->setParameter('relatedType', $type)
            ->setParameter('mainSlug', $mainSlug)
            ->setParameter('locales', $locales);

        return $query->getQuery()->getResult();
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
            ->andWhere('t.productType = :productType')
            ->setParameter('locale', $locale)
            ->setParameter('relatedType', $type)
            ->setParameter('productType', Tags::PRODUCT_TYPE_COLLECTION);

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
            ->innerJoin('t.tagTranslations', 'tt')
            ->where('tt.locale = :locale')
            ->andWhere('t.relatedType = :relatedType')
            ->setParameter('locale', $locale)
            ->setParameter('relatedType', $type);

        return $query->getQuery()->getResult();
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getByProduct(Product $product): array
    {
        $query = $this->createQueryBuilder('t')
            ->select(
                't.id'
            )
            ->innerJoin('t.tagTranslations', 'tt')
            ->innerJoin(ProductHasTags::class, 'pht', 'WITH', 'pht.tag = t.id AND pht.product = :product')
            ->where('tt.locale = \'rs\'')
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
            ->innerJoin('t.tagTranslations', 'tt')
            ->innerJoin(BlogHasTags::class, 'bht', 'WITH', 'bht.tag = t.id AND bht.blog = :blog')
            ->where('tt.locale = :locale')
            ->setParameter('locale', $locale)
            ->setParameter('blog', $blog);

        return $query->getQuery()->getResult();
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
            ->innerJoin('t.tagTranslations', 'tt')
            ->innerJoin(ProductHasTags::class, 'pht', 'WITH', 'pht.tag = tt.tag')
            ->innerJoin(Product::class, 'p', 'WITH', 'p = pht.product')
            ->where('pht.product IN (:products)')
            ->andWhere('tt.locale = :locale')
            ->setParameter('products', $products)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getResult();
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

    public function getForLocalization(string $slug)
    {
        $query = $this->createQueryBuilder('t')
            ->innerJoin(TagTranslation::class, 'tt')
            ->where('tt.slug = :slug')
            ->setParameter('slug', $slug);

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
