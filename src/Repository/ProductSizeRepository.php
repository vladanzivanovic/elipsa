<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductHasSizes;
use App\Entity\ProductSize;
use App\Entity\Tags;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
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

    /**
     * @param DataTableModel $tableModel
     *
     * @return array
     */
    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('ps')
            ->select(
                'ps.id',
                'ps.size',
                'ps.slug',
                'COUNT(phs.id) as total_used'
            )
            ->leftJoin('ps.productHasSizes', 'phs')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('ps.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection())
            ->groupBy('ps.id')
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
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

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getByProduct(Product $product): array
    {
        $query = $this->createQueryBuilder('ps')
            ->select(
                'ps.slug'
            )
            ->innerJoin(ProductHasSizes::class, 'phs', 'WITH', 'phs.size = ps AND phs.product = :product')
            ->setParameter('product', $product);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param array $products
     *
     * @return array
     */
    public function getByProducts(array $products): array
    {
        $query = $this->createQueryBuilder('ps')
            ->select(
                'p.id as productId',
                'ps.size'
            )
            ->innerJoin(ProductHasSizes::class, 'phs', 'WITH', 'phs.size = ps AND phs.isAvailable = :isAvailable')
            ->innerJoin(Product::class, 'p', 'WITH', 'p = phs.product')
            ->where('phs.product IN (:products)')
            ->setParameter('products', $products)
            ->setParameter('isAvailable', true);

        return $query->getQuery()->getArrayResult();
    }
}
