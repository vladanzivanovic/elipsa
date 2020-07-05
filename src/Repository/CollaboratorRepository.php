<?php

namespace App\Repository;

use App\Entity\Collaborator;
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
class CollaboratorRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Collaborator::class);
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
                'c.id as id',
                'c.email as email',
                'CONCAT(c.firstName, \' \', c.lastName) as full_name',
                'c.address as address',
                'c.phone as phone',
                'c.city as city',
                'c.country as country',
                'c.location as location',
                'c.numberOfFloors as no_floors',
                'c.shoppingMall as shopping_mall',
                'c.spaceSize as total_size',
                'c.store has_store',
                'c.website as website',
                'c.zipCode as zip_code',
                'presentation.id as presentation_doc',
                'plan.id as plan_doc'
            )
            ->leftJoin('c.presentation', 'presentation')
            ->leftJoin('c.plan', 'plan')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection());

        return $query->getQuery()->getArrayResult();
    }
}
