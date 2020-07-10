<?php

namespace App\Repository;

use App\Entity\Career;
use App\Model\DataTableModel;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Collaborator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Collaborator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Collaborator[]    findAll()
 * @method Collaborator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CareerRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Career::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as total')
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
        $query = $this->createQueryBuilder('c')
            ->select(
                'c.id',
                'c.email',
                'CONCAT(c.firstName, \' \', c.lastName) as full_name',
                'c.position',
                'cv.id as cv_doc',
                'c.accompanyingLetter as accompanying_letter'
            )
            ->leftJoin('c.cv', 'cv')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('c.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection());

        return $query->getQuery()->getArrayResult();
    }
}
