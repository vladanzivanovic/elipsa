<?php

namespace App\Repository;

use App\Entity\Blog;
use App\Entity\BlogHasTags;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class BlogHasTagsRepository
 *
 * @package SiteBundle\Repository
 */
class BlogHasTagsRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogHasTags::class);
    }

    /**
     * @param array $tags
     * @param Blog  $blog
     *
     * @return array
     */
    public function getTagsByBlog(Blog $blog): array
    {
        $query = $this->createQueryBuilder('bht')
            ->select(
                'bht.tag'
            )
            ->where('bht.blog = :blog')
            ->setParameter('blog', $blog);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param array $tags
     * @param Blog  $blog
     *
     * @return array
     */
    public function getTagsEntityByBlog(array $tags, Blog $blog): array
    {
        $query = $this->createQueryBuilder('bht')
            ->where('bht.tag IN (:tags)')
            ->andWhere('bht.blog = :blog')
            ->setParameter('tags', $tags)
            ->setParameter('blog', $blog);

        return $query->getQuery()->getResult();
    }
}
