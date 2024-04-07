<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductHasSizes;
use App\Entity\ProductSize;
use App\Entity\Tags;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method ProductSize|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductSize|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductSize[]    findAll()
 * @method ProductSize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductSizeRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductSize::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('ps')
            ->select('COUNT(ps.id) as total')
        ;

        return $query->getQuery()->getSingleScalarResult();
    }

    
    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('ps')
            ->select(
                'ps.id as id',
                'ps.size as size',
                'ps.slug as slug',
                'COUNT(phs.id) as total_used'
            )
            ->leftJoin('ps.productHasSizes', 'phs')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
            ->groupBy('ps.id')
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function getForOptions(): array
    {
        $query = $this->createQueryBuilder('ps')
            ->select(
                'ps.size as title',
                'ps.slug as value'
            )
        ;

        return $query->getQuery()->getArrayResult();
    }

    
    public function getByProducts(array $products): array
    {
        $query = $this->createQueryBuilder('ps')
            ->select(
                'p.id as productId',
                'ps.size'
            )
            ->innerJoin(ProductHasSizes::class, 'phs', 'WITH', 'phs.size = ps')
            ->innerJoin(Product::class, 'p', 'WITH', 'p = phs.product')
            ->where('phs.product IN (:products)')
            ->setParameter('products', $products)
            ->orderBy('ps.size');

        return $query->getQuery()->getArrayResult();
    }
}
