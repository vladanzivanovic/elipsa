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

    /**
     * @param DataTableModel $tableModel
     *
     * @return array
     */
    public function getAdminList(DataTableModel $tableModel, string $type): array
    {
        $query = $this->createQueryBuilder('pc')
            ->select(
                'pc.id as id',
                'pc.code as code',
                'pc.validFrom as validFrom',
                'pc.validTo as validTo',
                'pc.discount as discount'
            )
            ->where('pc.type = :type')
            ->setParameter('type', $type)
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $type
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
