<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductCleaning;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductCleaning|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductCleaning|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductCleaning[]    findAll()
 * @method ProductCleaning[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductCleaningRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductCleaning::class);
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getByProduct(Product $product): array
    {
        $query = $this->createQueryBuilder('pc')
            ->select(
                'pc.icon'
            )
            ->where('pc.product = :product')
            ->setParameter('product', $product);

        return $query->getQuery()->getArrayResult();
    }
}
