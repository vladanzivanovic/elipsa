<?php

namespace App\Repository;

use App\Entity\OfficeContactTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OfficeContactTranslation>
 *
 * @method OfficeContactTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfficeContactTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfficeContactTranslation[]    findAll()
 * @method OfficeContactTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficeContactTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OfficeContactTranslation::class);
    }

    public function add(OfficeContactTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(OfficeContactTranslation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return OfficeContactTranslation[] Returns an array of OfficeContactTranslation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OfficeContactTranslation
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
