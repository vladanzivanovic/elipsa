<?php

namespace App\Repository;

use App\Entity\ProductHasSizes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductHasSizes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductHasSizes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductHasSizes[]    findAll()
 * @method ProductHasSizes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductHasSizesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductHasSizes::class);
    }

    // /**
    //  * @return ProductHasSizes[] Returns an array of ProductHasSizes objects
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
    public function findOneBySomeField($value): ?ProductHasSizes
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
