<?php

namespace App\Repository;

use App\Entity\OrderProductTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OrderProductTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProductTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProductTranslation[]    findAll()
 * @method OrderProductTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProductTranslation::class);
    }

    // /**
    //  * @return OrderProductTranslation[] Returns an array of OrderProductTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?OrderProductTranslation
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
