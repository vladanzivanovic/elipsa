<?php

namespace App\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Blog;
use App\Entity\BlogHasTags;
use App\Entity\BlogTranslation;
use App\Model\DataTableModel;

/**
 * Class BlogRepository
 *
 * @package SiteBundle\Repository
 */
class BlogRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

    /**
     * @return string
     * @throws NonUniqueResultException
     */
    public function countBlog(): string
    {
        $query = $this->createQueryBuilder('blog')
            ->select('COUNT(blog.id) as total')
        ;

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param DataTableModel $tableModel
     *
     * @return array
     */
    public function getListForAdmin(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('blog')
            ->select(
                'blog.id',
                'blog.status',
                'bt.title',
                'bt.alias'
            )
            ->innerJoin(BlogTranslation::class, 'bt', 'WITH', 'bt.blog = blog AND bt.locale = :locale')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->setParameter('locale', 'rs')
            ->orderBy('blog.'.$tableModel->getOrderColumn(), $tableModel->getOrderDirection());

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string   $locale
     * @param int|null $tagIndex
     *
     * @return QueryBuilder
     */
    public function getDqlForPaginationPage(string $locale): QueryBuilder
    {
        $query = $this->createQueryBuilder('blog')
            ->select(
                'blog.id as productId',
                'DATE_FORMAT(blog.createdAt, \'%d\') as day',
                'DATE_FORMAT(blog.createdAt, \'%b\') as month',
                'DATE_FORMAT(blog.createdAt, \'%d.%m.%Y\') as date',
                'bt.title',
                'bt.shortDescription',
                'bt.alias',
                'image.name as imageName'
            )
            ->innerJoin('blog.blogTranslations', 'bt')
            ->innerJoin('blog.image', 'image')
            ->innerJoin('blog.blogHasTags', 'bht')
            ->where('blog.status = :status')
            ->andWhere('bt.locale = :locale')
            ->setParameter('status', Blog::STATUS_ACTIVE)
            ->setParameter('locale', $locale)
            ->orderBy('blog.createdAt', 'DESC')
            ->groupBy('blog.id');

//        if (is_int($tagIndex)) {
//            $subQuery = $this->_em->createQueryBuilder()
//                ->from(BlogHasTags::class, 'bht1')
//                ->select('1')
//                ->where('bht1.tag = :tagIndex')
//                ->andWhere('bht1.blog = blog');
//
//            $query->andWhere('EXISTS ('. $subQuery->getDQL() .')')
//                ->setParameter('tagIndex', $tagIndex);
//        }

        return $query;
    }
}
