<?php

namespace App\Repository;

use App\Entity\ProductColor;
use App\Entity\ProductTags;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method ProductTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductTags[]    findAll()
 * @method ProductTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductTagsRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductTags::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('pt')
            ->select('COUNT(pt.id) as total')
            ->where('pt.locale = \'rs\'')
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
        $query = $this->createQueryBuilder('pt')
            ->select(
                'pt.id',
                'pt.label as rs_name',
                'pt.mainSlug',
                'pten.label as en_name',
                'pt.slug'
            )
            ->innerJoin(ProductTags::class, 'pten', 'WITH', 'pten.mainSlug = pt.mainSlug AND pten.locale = \'en\'')
            ->where('pt.locale = \'rs\'')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('pt.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $mainSlug
     * @param array  $locales
     *
     * @return array
     */
    public function getByMainSlugAndLocales(string $mainSlug, array $locales): array
    {
        $query = $this->createQueryBuilder('pt')
            ->select(
                'pt.label',
                'pt.locale'
            )
            ->where('pt.mainSlug = :mainSlug')
            ->andWhere('pt.locale IN (:locales)')
            ->setParameter('mainSlug', $mainSlug)
            ->setParameter('locales', $locales);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $mainSlug
     *
     * @return void
     */
    public function remove(string $mainSlug): void
    {
        $query = $this->createQueryBuilder('pt')
            ->delete()
            ->where('pt.mainSlug = :mainSlug')
            ->setParameter('mainSlug', $mainSlug);

        $query->getQuery()->execute();
    }

    /**
     * @return array
     */
    public function getForOptions(): array
    {
        $query = $this->createQueryBuilder('pt')
            ->select(
                'pt.mainSlug as value',
                'pt.label as title'
            )
            ->where('pt.locale = \'rs\'');

        return $query->getQuery()->getArrayResult();
    }
}
