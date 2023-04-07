<?php

namespace App\Repository;

use App\Entity\BannerTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BannerTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method BannerTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method BannerTranslation[]    findAll()
 * @method BannerTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BannerTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BannerTranslation::class);
    }

    // /**
    //  * @return BannerTranslation[] Returns an array of BannerTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BannerTranslation
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
