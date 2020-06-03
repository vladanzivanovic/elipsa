<?php

namespace App\Repository;

use App\Entity\Loyalty;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Loyalty|null find($id, $lockMode = null, $lockVersion = null)
 * @method Loyalty|null findOneBy(array $criteria, array $orderBy = null)
 * @method Loyalty[]    findAll()
 * @method Loyalty[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LoyaltyRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loyalty::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('l')
            ->select('COUNT(l.id) as total')
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
        $query = $this->createQueryBuilder('l')
            ->addSelect(
                'l.id',
                'DATE_FORMAT(l.birthDate, \'%d.%m.%Y\') as birth_date',
                'l.email',
                'CONCAT(l.firstName, \' \', l.lastName) as full_name',
                'l.occupation',
                'l.mobilePhone as mobile_phone',
                'l.note',
                'l.rate'
            )
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('l.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection());

        return $query->getQuery()->getArrayResult();
    }
}
