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
                'bt.alias',
                'blog.availableCountries as available_countries'
            )
            ->innerJoin(BlogTranslation::class, 'bt', 'WITH', 'bt.blog = blog AND (bt.locale = :rsLocale OR bt.locale = :baLocale)')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->setParameter('rsLocale', 'rs')
            ->setParameter('baLocale', 'ba')
            ->groupBy('blog.id')
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection());

        return $query->getQuery()->getArrayResult();
    }

    public function getDqlForPaginationPage(string $countryCode, null|string $tagSlug): QueryBuilder
    {
        $query = $this->createQueryBuilder('blog')
            ->where('blog.status = :status')
            ->andWhere('blog.availableCountries LIKE :country')
            ->setParameter('status', Blog::STATUS_ACTIVE)
            ->setParameter('country', '%'.$countryCode.'%')
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
