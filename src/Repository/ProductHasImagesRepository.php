<?php

namespace App\Repository;

use App\Entity\ProductColor;
use App\Entity\ProductHasImages;
use App\Entity\Resources\StatusInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductHasImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductHasImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductHasImages[]    findAll()
 * @method ProductHasImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductHasImagesRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductHasImages::class);
    }

    public function colorsInUseByProductStatus(ProductColor $productColor, int $status): bool
    {
        $query = $this->createQueryBuilder('phi')
            ->select('COUNT(phi.id) as total')
            ->innerJoin('phi.product', 'p')
            ->where('phi.color = :color')
            ->andWhere('p.status = :status')
            ->setParameter('color', $productColor)
            ->setParameter('status', $status);

        $result = $query->getQuery()->getSingleScalarResult();

        return $result > 0;
    }
}
