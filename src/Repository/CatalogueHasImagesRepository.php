<?php

namespace App\Repository;

use App\Entity\CatalogueHasImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CatalogueHasImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method CatalogueHasImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method CatalogueHasImages[]    findAll()
 * @method CatalogueHasImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogueHasImagesRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogueHasImages::class);
    }

    // /**
    //  * @return CatalogueHasImages[] Returns an array of CatalogueHasImages objects
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
    public function findOneBySomeField($value): ?CatalogueHasImages
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
