<?php

namespace App\Repository;

use App\Entity\ProductHasColor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductHasColor|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductHasColor|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductHasColor[]    findAll()
 * @method ProductHasColor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductHasColorRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductHasColor::class);
    }
}
