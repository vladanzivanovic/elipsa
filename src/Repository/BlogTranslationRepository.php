<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Blog;
use App\Entity\BlogTranslation;

/**
 * Class BlogTranslationRepository
 *
 * @package SiteBundle\Repository
 */
class BlogTranslationRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogTranslation::class);
    }

    /**
     * @param $locale
     *
     * @return BlogTranslation[] | array
     */
    public function getBlogForSiteMap($locale)
    {
        $query = $this->createQueryBuilder('bt')
            ->innerJoin('bt.blog', 'blog')
            ->where('blog.status = :status')
            ->andWhere('bt.locale = :locale')
            ->setParameter('status', Blog::STATUS_ACTIVE)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getResult();
    }

    /**
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBlogAliasByCurrentLocale(string $alias, string $locale)
    {
        $query = $this->createQueryBuilder('bt')
            ->select('bt2.alias')
            ->innerJoin(BlogTranslation::class, 'bt2', 'WITH', 'bt2.blog = bt.blog AND bt2.locale = :locale')
            ->where('bt.alias = :alias')
            ->setParameter('locale', $locale)
            ->setParameter('alias', $alias);

        return $query->getQuery()->getSingleScalarResult();
    }
}
