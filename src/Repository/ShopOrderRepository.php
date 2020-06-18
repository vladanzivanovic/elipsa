<?php

namespace App\Repository;

use App\Entity\ShopOrder;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ShopOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShopOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShopOrder[]    findAll()
 * @method ShopOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShopOrderRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShopOrder::class);
    }
}
