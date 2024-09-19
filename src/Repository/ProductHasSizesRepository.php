<?php

namespace App\Repository;

use App\Entity\ProductHasSizes;
use App\Entity\ProductSize;
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

    public function sizeExistsInProductsByStatus(ProductSize $productSize, int $status): bool
    {
        $query = $this->createQueryBuilder('phs')
            ->select('count(phs.id)')
            ->innerJoin('phs.productOption', 'po')
            ->innerJoin('po.product', 'p')
            ->where('phs.size = :productSize')
            ->andWhere('p.status = :status')
            ->setParameter('productSize', $productSize)
            ->setParameter('status', $status);

        $result = $query->getQuery()->getSingleScalarResult();

        return $result > 0;
    }
}
