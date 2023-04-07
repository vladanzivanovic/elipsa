<?php

namespace App\Repository;

use App\Entity\ProductHasSizes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductHasSizes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductHasSizes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductHasSizes[]    findAll()
 * @method ProductHasSizes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductHasSizesRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductHasSizes::class);
    }
}
