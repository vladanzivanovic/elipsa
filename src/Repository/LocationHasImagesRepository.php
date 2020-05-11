<?php

namespace App\Repository;

use App\Entity\LocationHasImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LocationHasImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocationHasImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocationHasImages[]    findAll()
 * @method LocationHasImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationHasImagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationHasImages::class);
    }

    // /**
    //  * @return LocationHasImages[] Returns an array of LocationHasImages objects
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
    public function findOneBySomeField($value): ?LocationHasImages
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
