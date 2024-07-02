<?php

namespace App\Repository;

use App\Entity\OrderProduct;
use App\Entity\OrderProductTranslation;
use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ShopOrder;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    
    public function getByUser(User $user, string $locale, string $country): array
    {
        $query = $this->createQueryBuilder('op')
            ->select(
                'op.id',
                'op.price',
                'op.discount',
                'op.quantity',
                'op.size',
                'opt.title',
                'opt.slug',
                'image.name as image_name',
                'op.quantity',
                'color.hex'
            )
            ->innerJoin( ShopOrder::class, 'o', 'WITH',  'o.id = op.orderId and o.country = :country')
            ->innerJoin(OrderProductTranslation::class, 'opt', 'WITH', 'opt.orderProduct = op')
            ->innerJoin('op.image', 'image')
            ->innerJoin('op.color', 'color')
            ->where('o.user = :user')
            ->andWhere('opt.locale = :locale')
            ->andWhere('o.status = :completedStatus')
            ->setParameter('user', $user)
            ->setParameter('locale', $locale)
            ->setParameter('completedStatus', ShopOrder::STATUS_COMPLETED)
            ->setParameter('country', $country)
            ->orderBy('o.completedAt', 'DESC')
            ->groupBy('op.id');

        return $query->getQuery()->getArrayResult();
    }

    /**
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
