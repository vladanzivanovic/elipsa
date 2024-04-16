<?php

namespace App\Repository;

use App\Entity\TagTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TagTranslation>
 *
 * @method TagTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagTranslation[]    findAll()
 * @method TagTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagTranslationRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TagTranslation::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getForLocalization(string $slug, string $locale): bool|float|int|string|null
    {
        $query = $this->createQueryBuilder('tt')
            ->select(
                'tt2.slug'
            )
            ->innerJoin(TagTranslation::class, 'tt2', 'WITH', 'tt2.tag = tt.tag')
            ->where('tt.slug = :slug')
            ->andWhere('tt2.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getSingleScalarResult();
    }
}
