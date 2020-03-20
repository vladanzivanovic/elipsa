<?php

namespace App\Repository;

use App\Entity\Image;
use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ProductHasImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    /**
     * @param Product $product
     * @param int     $relatedToType
     */
    public function removeMainImage(Product $product, int $relatedToType): void
    {
        $subQuery = $this->_em->createQueryBuilder()
            ->select('1')
            ->from(ProductHasImages::class, 'phi')
            ->where('phi.image = i')
            ->andWhere('phi.product = :product');

        $this->createQueryBuilder('i')
            ->update()
            ->set('i.isMain', 0)
            ->innerJoin(ProductHasImages::class, 'phi', 'WITH', 'phi.image = i')
            ->where('i.relatedToType = :relatedToType')
            ->andWhere('EXISTS ('.$subQuery->getDQL().')')
            ->setParameter('product', $product)
            ->setParameter('relatedToType', $relatedToType)
            ->getQuery()
            ->execute();
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getByProduct(Product $product): array
    {
        $query = $this->createQueryBuilder('i')
            ->select(
                'i.id',
                'i.name as fileName',
                'i.isMain',
                'pc.mainSlug as color'
            )
            ->innerJoin(ProductHasImages::class, 'phi', 'WITH', 'phi.image = i AND phi.product = :product')
            ->innerJoin(ProductColor::class, 'pc', 'WITH', 'phi.color = pc')
            ->setParameter('product', $product);

        return $query->getQuery()->getArrayResult();
    }
}
