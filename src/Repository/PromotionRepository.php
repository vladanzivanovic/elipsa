<?php

namespace App\Repository;

use App\Entity\Promotion;
use App\Model\DataTableModel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
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

    
    public function getAdminList(DataTableModel $tableModel, string $type): array
    {
        $generalSearch = $tableModel->getGeneralSearch();
        $typeSearch = $tableModel->getColumnSearchValue('type');

        $query = $this->createQueryBuilder('pc')
            ->select(
                'pc.id as id',
                'pc.code as code',
                'pc.validFrom as validFrom',
                'pc.validTo as validTo',
                'pc.discount as discount',
                'pc.type as type'
            )
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;


        if ($typeSearch instanceof \App\Model\DataTableColumnModel) {
            $query->where('pc.type = :type')
            ->setParameter('type', $typeSearch->getSearchValue());
        }

        if (null !== $generalSearch) {
            $query->andWhere('pc.code LIKE :generalSearch')
                ->setParameter('generalSearch', '%'.$generalSearch.'%');
        }

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return Promotion[]
     */
    public function getActivePromotionsByType(string $type): array
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.validTo >= :now')
            ->andWhere('p.validFrom <= :now')
            ->andWhere('p.type = :type')
            ->setParameter('now', $now)
            ->setParameter('type', $type);

        return $query->getQuery()->getResult();
    }
}
