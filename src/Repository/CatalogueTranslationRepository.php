<?php

namespace App\Repository;

use App\Entity\CatalogueTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CatalogueTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CatalogueTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CatalogueTranslation[]    findAll()
 * @method CatalogueTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogueTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogueTranslation::class);
    }

    // /**
    //  * @return CatalogueTranslation[] Returns an array of CatalogueTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CatalogueTranslation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
