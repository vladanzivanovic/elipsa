<?php

namespace App\Repository;

use App\Entity\ColorTranslation;
use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ProductHasImages;
use App\Entity\Resources\StatusInterface;
use App\Model\DataTableModel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method ProductColor|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductColor|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductColor[]    findAll()
 * @method ProductColor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductColorRepository extends ExtendedEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly string $defaultLocale,
    ){
        parent::__construct($registry, ProductColor::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countData(): mixed
    {
        $query = $this->createQueryBuilder('pc')
            ->select('COUNT(pc.id) as total')
            ->where('pc.status = :status')
            ->setParameter('status', StatusInterface::STATUS_ACTIVE);

        return $query->getQuery()->getSingleScalarResult();
    }

    
    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('pc')
            ->select(
                'pc.id as id',
                'pc.hex as hex',
                'ctRs.title as rs_name',
                'ctEn.title as en_name',
            )
            ->innerJoin(ColorTranslation::class, 'ctRs', 'WITH', 'ctRs.locale = \'rs\' AND ctRs.color = pc')
            ->innerJoin(ColorTranslation::class, 'ctEn', 'WITH', 'ctEn.locale = \'en\' AND ctEn.color = pc')
            ->where('pc.status = :status')
            ->setParameter('status', StatusInterface::STATUS_ACTIVE)
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
            ->groupBy('pc.id')
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function getForOptions(): array
    {
        $query = $this->createQueryBuilder('pc')
            ->select(
                'pc.hex',
                'pct.title',
                'pc.id as value'
            )
            ->innerJoin('pc.translations', 'pct')
            ->where('pct.locale = :defaultLocale')
            ->andWhere('pc.status = :status')
            ->setParameter('defaultLocale', $this->defaultLocale)
            ->setParameter('status', StatusInterface::STATUS_ACTIVE);

        return $query->getQuery()->getArrayResult();
    }

    public function getByLocale(string $locale): array
    {
        $query = $this->createQueryBuilder('pc')
            ->select(
                'pc.id',
                'pc.hex',
                'ct.title',
                'ct.slug'
            )
            ->innerJoin('pc.translations', 'ct')
            ->where('ct.locale = :locale')
            ->andWhere('pc.status = :status')
            ->setParameter('locale', $locale)
            ->setParameter('status', StatusInterface::STATUS_ACTIVE);

        return $query->getQuery()->getArrayResult();
    }

    
    public function getByProducts(array $products, string $locale): array
    {
        $query = $this->createQueryBuilder('pc')
            ->select(
                'DISTINCT pc.hex',
                'ct.slug',
                'p.id as productId',
                'pc.id'
            )
            ->innerJoin('pc.translations', 'ct')
            ->innerJoin(ProductHasImages::class, 'phi', 'WITH', 'phi.color = pc')
            ->innerJoin(Product::class, 'p', 'WITH', 'p = phi.product')
            ->where('phi.product IN (:products)')
            ->andWhere('ct.locale = :locale')
            ->setParameter('products', $products)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getArrayResult();
    }
}
