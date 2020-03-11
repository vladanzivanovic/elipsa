<?php

namespace App\Repository;

use App\Entity\ProductColor;
use App\Entity\ProductTranslation;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @method ProductColor|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductColor|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductColor[]    findAll()
 * @method ProductColor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductColorRepository extends ExtendedEntityRepository
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * ProductColorRepository constructor.
     *
     * @param ManagerRegistry  $registry
     * @param SessionInterface $session
     */
    public function __construct(ManagerRegistry $registry, SessionInterface $session)
    {
        parent::__construct($registry, ProductColor::class);
        $this->session = $session;
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
            ->where('pc.locale = \'rs\'')
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
        $query = $this->createQueryBuilder('pc')
            ->select(
                'pc.id',
                'pc.hex',
                'pc.name as rs_name',
                'pc.mainSlug',
                'pcen.name as en_name',
                'pc.slug'
            )
            ->innerJoin(ProductColor::class, 'pcen', 'WITH', 'pcen.mainSlug = pc.mainSlug AND pcen.locale = \'en\'')
            ->where('pc.locale = \'rs\'')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('pc.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection())
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
        $query = $this->createQueryBuilder('pc')
            ->select(
                'pc.name',
                'pc.locale'
            )
            ->where('pc.mainSlug = :mainSlug')
            ->andWhere('pc.locale IN (:locales)')
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
        $query = $this->createQueryBuilder('pc')
            ->delete()
            ->where('pc.mainSlug = :mainSlug')
            ->setParameter('mainSlug', $mainSlug);

        $query->getQuery()->execute();
    }
}
