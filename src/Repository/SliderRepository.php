<?php

namespace App\Repository;

use App\Entity\Image;
use App\Entity\Slider;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Slider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slider[]    findAll()
 * @method Slider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SliderRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slider::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('s')
            ->select('COUNT(s.id) as total')
        ;

        return $query->getQuery()->getSingleScalarResult();
    }

    
    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                's.id as id',
                's.position as position',
                's.isActive as is_active',
                'image.name',
                's.availableCountries as available_countries'
            )
            ->innerJoin('s.image', 'image')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getLastPosition(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                's.position'
            )
            ->orderBy('s.position', 'DESC')
            ->setMaxResults(1);

        return $query->getQuery()->getScalarResult();
    }

    
    public function getHigherThenPosition(int $position): array
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.position > :position')
            ->setParameter('position', $position);

        return $query->getQuery()->getResult();
    }

    /**
     * @return array<int, Slider>
     */
    public function getRandomActiveSlider(string $host): array
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.isActive = :isActive')
            ->andWhere('s.availableCountries LIKE :host')
            ->setParameter('isActive', true)
            ->setParameter('host', '%'.$host.'%')
            ->orderBy('RAND()');

        return $query->getQuery()->getResult();
    }
}
