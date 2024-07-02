<?php

namespace App\Repository;

use App\Entity\ProductOptions;
use App\Entity\ProductTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductTranslation[]    findAll()
 * @method ProductTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductTranslation::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getByCountryCode(string $slug, string $countryCode): ProductTranslation
    {
        $query = $this->createQueryBuilder('pt')
            ->innerJoin(ProductOptions::class, 'po', 'WITH', 'po.product = pt.product')
            ->where('pt.slug = :slug')
            ->andWhere('po.country = :country')
            ->setParameter('slug', $slug)
            ->setParameter('country', $countryCode);

        return  $query->getQuery()->getSingleResult();
    }

    // /**
    //  * @return ProductTranslation[] Returns an array of ProductTranslation objects
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
    public function findOneBySomeField($value): ?ProductTranslation
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
