<?php

namespace App\Repository;

use App\Entity\Career;
use App\Entity\CareerDescriptionTranslation;
use App\Model\DataTableModel;
use Doctrine\Persistence\ManagerRegistry;
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

    
    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('c')
            ->select(
                'c.id as id',
                'c.email as email',
                'CONCAT(c.firstName, \' \', c.lastName) as full_name',
                'cdt.title as position',
                'DATE_FORMAT(c.createdAt, \'%d.%m.%Y\') as applying_date'
            )
            ->leftJoin('c.cv', 'cv')
            ->innerJoin(CareerDescriptionTranslation::class, 'cdt', 'WITH', 'cdt.careerDescription = c.position AND cdt.locale = \'rs\'')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection());

        return $query->getQuery()->getArrayResult();
    }
}
