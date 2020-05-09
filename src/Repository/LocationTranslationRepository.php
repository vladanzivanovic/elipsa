<?php

namespace App\Repository;

use App\Entity\LocationTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LocationTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocationTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocationTranslation[]    findAll()
 * @method LocationTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationTranslation::class);
    }

    // /**
    //  * @return LocationTranslation[] Returns an array of LocationTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LocationTranslation
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
