<?php

namespace App\Repository;

use App\Entity\Banner;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Banner|null find($id, $lockMode = null, $lockVersion = null)
 * @method Banner|null findOneBy(array $criteria, array $orderBy = null)
 * @method Banner[]    findAll()
 * @method Banner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BannerRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Banner::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('b')
            ->select('COUNT(b.id) as total')
        ;

        return $query->getQuery()->getSingleScalarResult();
    }

    
    public function getAdminList(DataTableModel $tableModel, array $types): array
    {
        $query = $this->createQueryBuilder('b')
            ->select(
                'b.id as id',
                'b.position as position',
                'b.isActive as is_active',
                'b.type as type',
                'image.name'
            )
            ->innerJoin('b.image', 'image')
            ->where('b.type IN (:types)')
            ->setParameter('types', $types)
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function getActiveByType(int $type, string $locale)
    {
        $query = $this->createQueryBuilder('b')
            ->where('b.isActive = :isActive')
            ->andWhere('b.type = :type')
            ->andWhere('b.availableCountries LIKE :locale')
            ->setParameter('isActive', true)
            ->setParameter('type', $type)
            ->setParameter('locale', '%'.$locale.'%')
            ->orderBy('b.position');

        return $query->getQuery()->getResult();
    }
}
