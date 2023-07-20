<?php

namespace App\Repository;

use App\Entity\Catalogue;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Catalogue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Catalogue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Catalogue[]    findAll()
 * @method Catalogue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatalogueRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Catalogue::class);
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
                'ct.title as title',
                'c.status as status'
            )
            ->innerJoin('c.catalogueTranslations', 'ct')
            ->where('ct.locale = \'rs\'')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    public function getCatalogPage(string $locale): array
    {
        $query = $this->createQueryBuilder('c')
            ->innerJoin('c.catalogueTranslations', 'ct')
            ->where('ct.locale = :locale')
            ->andWhere('c.status = :activeStatus')
            ->setParameter('locale', $locale)
            ->setParameter('activeStatus', Catalogue::STATUS_ACTIVE);

        return $query->getQuery()->getResult();
    }
}
