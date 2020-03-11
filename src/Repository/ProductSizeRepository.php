<?php

namespace App\Repository;

use App\Entity\ProductSize;
use App\Entity\ProductTags;
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
                'ps.slug'
            )
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('ps.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }
}
