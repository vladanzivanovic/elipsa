<?php

namespace App\Repository;

use App\Entity\ProductHasTags;
use App\Entity\Tags;
use App\Entity\TagTranslation;
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
     * @throws NonUniqueResultException
     */
    public function countBlog(): string
    {
        $query = $this->createQueryBuilder('blog')
            ->select('COUNT(blog.id) as total')
        ;

        return $query->getQuery()->getSingleScalarResult();
    }

    
    public function getListForAdmin(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('blog')
            ->select(
                'blog.id as id',
                'blog.status as status',
                'bt.title as title',
                'bt.alias'
            )
            ->innerJoin(BlogTranslation::class, 'bt', 'WITH', 'bt.blog = blog AND bt.locale = :locale')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->setParameter('locale', 'rs')
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection());

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string|null $tagSlug
     *
     */
    public function getDqlForPaginationPage(string $locale, null|string $tagSlug): QueryBuilder
    {
        $query = $this->createQueryBuilder('blog')
//            ->select(
//                'blog.id as productId',
//                'DATE_FORMAT(blog.createdAt, \'%d\') as day',
//                'DATE_FORMAT(blog.createdAt, \'%b\') as month',
//                'DATE_FORMAT(blog.createdAt, \'%d.%m.%Y\') as date',
//                'bt.title',
//                'bt.shortDescription',
//                'bt.alias',
//                'image.name as imageName'
//            )
//            ->innerJoin('blog.blogTranslations', 'bt')
//            ->innerJoin('blog.image', 'image')
//            ->innerJoin('blog.blogHasTags', 'bht')
            ->where('blog.status = :status')
//            ->andWhere('bt.locale = :locale')
            ->setParameter('status', Blog::STATUS_ACTIVE)
//            ->setParameter('locale', $locale)
            ->orderBy('blog.createdAt', 'DESC')
            ->groupBy('blog.id');

        if (null !== $tagSlug) {
            $tagsQuery = $this->_em->createQueryBuilder()
                ->select('tt')
                ->from(TagTranslation::class, 'tt')
                ->innerJoin(BlogHasTags::class, 'bht', 'WITH', 'bht.tag = tt.tag')
                ->where('tt.slug = :tagSlug')
                ->andWhere('bht.blog = blog');
                ;

            $query->andWhere('EXISTS ('. $tagsQuery->getDQL() .')')
                ->setParameter('tagSlug', $tagSlug);
        }

        return $query;
    }
}
