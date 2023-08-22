<?php

namespace App\Repository;

use App\Entity\AskUs;
use App\Model\DataTableModel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method AskUs|null find($id, $lockMode = null, $lockVersion = null)
 * @method AskUs|null findOneBy(array $criteria, array $orderBy = null)
 * @method AskUs[]    findAll()
 * @method AskUs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AskUsRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AskUs::class);
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
        $query = $this->createQueryBuilder('au')
            ->addSelect(
                'au.id',
                'au.email',
                'CONCAT(au.firstName, \' \', au.lastName) as full_name',
                'au.subject as subject',
                'au.note'
            )
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('au.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection());

        return $query->getQuery()->getArrayResult();
    }
}
