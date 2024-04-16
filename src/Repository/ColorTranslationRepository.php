<?php

namespace App\Repository;

use App\Entity\ColorTranslation;
use App\Entity\Tags;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method ColorTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ColorTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ColorTranslation[]    findAll()
 * @method ColorTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColorTranslationRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ColorTranslation::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getForLocalization(string $slug, string $locale): bool|float|int|string|null
    {
        $query = $this->createQueryBuilder('ct')
            ->select(
                'ctt.slug'
            )
            ->innerJoin(ColorTranslation::class, 'ctt', 'WITH', 'ctt.color = ct.color')
            ->where('ct.slug = :slug')
            ->andWhere('ctt.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getSingleScalarResult();
    }
}
