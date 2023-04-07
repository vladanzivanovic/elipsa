<?php

namespace App\Repository;

use App\Entity\PromotionCoupon;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method PromotionCoupon|null find($id, $lockMode = null, $lockVersion = null)
 * @method PromotionCoupon|null findOneBy(array $criteria, array $orderBy = null)
 * @method PromotionCoupon[]    findAll()
 * @method PromotionCoupon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionCouponRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromotionCoupon::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('pc')
            ->select('COUNT(pc.id) as total')
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
        $query = $this->createQueryBuilder('pc')
            ->select(
                'pc.id as id',
                'pc.code as code',
                'pc.validFrom as validFrom',
                'pc.validTo as validTo',
                'pc.discount as discount'
            )
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }
}
