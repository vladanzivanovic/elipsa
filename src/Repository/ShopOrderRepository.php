<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ShopOrder;
use App\Entity\User;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

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

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('o')
            ->select('COUNT(o.id) as total')
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
        $query = $this->createQueryBuilder('o')
            ->addSelect(
                'o.id as id',
                'ba.email as email',
                'CONCAT(ba.firstName, \' \', ba.lastName) as full_name',
                'o.paymentType as payment_type',
                'o.status as status'
            )
            ->innerJoin('o.billingAddress', 'ba')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection());

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $token
     *
     * @return ShopOrder
     * @throws NonUniqueResultException
     */
    public function getByToken(string $token): ShopOrder
    {
        $query = $this->createQueryBuilder('o')
            ->where('o.token = :token')
            ->setParameter('token', $token);

        return $query->getQuery()->getOneOrNullResult();
    }
}
