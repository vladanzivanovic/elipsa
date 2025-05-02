<?php

namespace App\Repository;

use App\Entity\ProductHasCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductHasCategories|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductHasCategories|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductHasCategories[]    findAll()
 * @method ProductHasCategories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductHasCategoriesRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductHasCategories::class);
    }
}
