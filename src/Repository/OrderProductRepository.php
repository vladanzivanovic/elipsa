<?php

namespace App\Repository;

use App\Entity\OrderProduct;
use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ShopOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method OrderProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderProduct[]    findAll()
 * @method OrderProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderProductRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    /**
     * @param ShopOrder    $order
     * @param string       $locale
     *
     * @return array
     */
    public function getByOrder(ShopOrder $order, string $locale): array
    {
        $query = $this->createQueryBuilder('op')
            ->select(
                'op.id',
                'p.price',
                'p.discount',
                'pt.title',
                'pt.slug',
                'image.name as image_name',
                'op.quantity'
            )
            ->innerJoin('op.product', 'p')
            ->innerJoin('p.productTranslations', 'pt')
            ->innerJoin('p.productHasImages', 'phi')
            ->innerJoin('phi.image', 'image')
            ->where('op.orderId = :order')
            ->andWhere('pt.locale = :locale')
            ->andWhere('op.color = phi.color')
            ->setParameter('order', $order)
            ->setParameter('locale', $locale)
            ->groupBy('op.id');

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param ShopOrder    $order
     * @param Product      $product
     * @param string       $size
     * @param ProductColor $color
     *
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getByArguments(ShopOrder $order, Product $product, string $size, ProductColor $color)
    {
        $query = $this->createQueryBuilder('op')
            ->select(
                'op.id'
            )
            ->where('op.orderId = :order')
            ->andWhere('op.product = :product')
            ->andWhere('op.size = :size')
            ->andWhere('op.color = :color')
            ->setParameter('order', $order)
            ->setParameter('product', $product)
            ->setParameter('size', $size)
            ->setParameter('color', $color);

        return $query->getQuery()->getSingleScalarResult();
    }
}
