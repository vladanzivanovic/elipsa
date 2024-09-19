<?php

namespace App\Repository;

use App\Entity\Promotion;
use App\Entity\Resources\StatusInterface;
use App\Model\DataTableModel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionRepository extends ExtendedEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly RequestStack $requestStack
    ) {
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
            ->where('pc.status = :status')
            ->setParameter('status', StatusInterface::STATUS_ACTIVE)
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
            ->where('pc.status = :status')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->setParameter('status', StatusInterface::STATUS_ACTIVE)
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
     * @throws NonUniqueResultException
     */
    public function getByCodeAndStatus(string $code, string $status = StatusInterface::STATUS_ACTIVE): null|Promotion
    {
        $countryCode = $this->requestStack->getCurrentRequest()->attributes->get('_country');

        $query = $this->createQueryBuilder('p')
            ->where('p.code = :code')
            ->andWhere('p.availableCountries LIKE :countryCode')
            ->andWhere('p.status = :status')
            ->setParameter('code', $code)
            ->setParameter('countryCode', '%'.$countryCode.'%')
            ->setParameter('status', $status);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Promotion[]
     */
    public function getActivePromotionsByType(string $type): array
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $countryCode = $this->requestStack->getCurrentRequest()->attributes->get('_country');

        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('p.validTo >= :now')
            ->andWhere('p.validFrom <= :now')
            ->andWhere('p.type = :type')
            ->andWhere('p.availableCountries LIKE :countryCode')
            ->setParameter('now', $now)
            ->setParameter('type', $type)
            ->setParameter('countryCode', '%'.$countryCode.'%');

        return $query->getQuery()->getResult();
    }
}
