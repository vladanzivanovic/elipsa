<?php

namespace App\Repository;

use App\Entity\Banner;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
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

    /**
     * @param DataTableModel $tableModel
     * @param array|null     $collection
     *
     * @return array
     */
    public function getAdminList(DataTableModel $tableModel, ?array $collection): array
    {
        $query = $this->createQueryBuilder('b')
            ->select(
                'b.id',
                'b.position',
                'b.isActive as is_active',
                'image.name'
            )
            ->innerJoin('b.image', 'image')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('b.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        if (null !== $collection) {
            $query->andWhere('b.position IN (:positions)')
                ->setParameter('positions', $collection);
        }

        return $query->getQuery()->getArrayResult();
    }

    public function getActiveOrderByPosition(string $locale)
    {
        $query = $this->createQueryBuilder('b')
            ->select(
                'b.id',
                'b.position',
                'bt.description',
                'bt.buttonLink as button_link',
                'bt.buttonText as button_text',
                'i.name as image'
            )
            ->innerJoin('b.bannerTranslations', 'bt')
            ->innerJoin('b.image', 'i')
            ->where('b.isActive = :isActive')
            ->andWhere('bt.locale = :locale')
            ->setParameter('isActive', true)
            ->setParameter('locale', $locale)
            ->orderBy('b.position');

        return $query->getQuery()->getArrayResult();
    }
}
