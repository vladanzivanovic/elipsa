<?php

namespace App\Repository;

use App\Entity\ProductHasTags;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductHasTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductHasTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductHasTags[]    findAll()
 * @method ProductHasTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductHasTagsRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductHasTags::class);
    }

    // /**
    //  * @return ProductHasTags[] Returns an array of ProductHasTags objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductHasTags
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
