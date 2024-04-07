<?php

namespace App\Repository;

use App\Entity\Blog;
use App\Entity\BlogHasImages;
use App\Entity\Catalogue;
use App\Entity\CatalogueHasImages;
use App\Entity\Image;
use App\Entity\Location;
use App\Entity\LocationHasImages;
use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\ProductHasImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

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
     * @return Image[]
     */
    public function getProductImages(Product $product): array
    {
        $query = $this->createQueryBuilder('i')
            ->innerJoin(ProductHasImages::class, 'phi', 'WITH', 'phi.image = i')
            ->where('phi.product = :product')
            ->setParameter('product', $product);

        return $query->getQuery()->getResult();

    }

    /**
     * @return Image[]
     */
    public function getCatalogImages(Catalogue $catalogue): array
    {
        $query = $this->createQueryBuilder('i')
            ->innerJoin(CatalogueHasImages::class, 'chi', 'WITH', 'chi.image = i')
            ->where('chi.catalogue = :catalogue')
            ->setParameter('catalogue', $catalogue);

        return $query->getQuery()->getResult();

    }

    /**
     * @return Image[]
     */
    public function getLocationImages(Location $location): array
    {
        $query = $this->createQueryBuilder('i')
            ->innerJoin(LocationHasImages::class, 'lhi', 'WITH', 'lhi.image = i')
            ->where('lhi.location = :location')
            ->setParameter('location', $location);

        return $query->getQuery()->getResult();

    }

    
    public function getByProduct(Product $product): array
    {
        $query = $this->createQueryBuilder('i')
            ->select(
                'i.id',
                'i.name as fileName',
                'i.isMain',
                'pc.id as color'
            )
            ->innerJoin(ProductHasImages::class, 'phi', 'WITH', 'phi.image = i AND phi.product = :product')
            ->innerJoin(ProductColor::class, 'pc', 'WITH', 'phi.color = pc')
            ->setParameter('product', $product)
            ->orderBy('pc.id');

        return $query->getQuery()->getArrayResult();
    }

    
    public function getByType(int $type): array
    {
        $query = $this->createQueryBuilder('i')
            ->select(
                'i.id',
                'i.name as fileName',
                'i.isMain',
            )
            ->where('i.relatedToType = :type')
            ->setParameter('type', $type)
            ->orderBy('i.id');

        return $query->getQuery()->getArrayResult();
    }

    
    public function getByBlog(Blog $blog): array
    {
        $query = $this->createQueryBuilder('i')
            ->select(
                'i.id',
                'i.name as fileName',
                'i.isMain'
            )
            ->innerJoin(Blog::class, 'b', 'WITH', 'b.image = i AND b.blog = :blog')
            ->setParameter('blog', $blog);

        return $query->getQuery()->getArrayResult();
    }

    
    public function getByLocation(Location $location): array
    {
        $query = $this->createQueryBuilder('i')
            ->select(
                'i.id',
                'i.name as fileName',
                'i.isMain',
            )
            ->innerJoin(LocationHasImages::class, 'lhi', 'WITH', 'lhi.image = i AND lhi.location = :location')
            ->setParameter('location', $location);

        return $query->getQuery()->getArrayResult();
    }

    
    public function getByCatalog(Catalogue $catalogue): array
    {
        $query = $this->createQueryBuilder('i')
            ->select(
                'i.id',
                'i.name as fileName',
                'i.isMain',
            )
            ->innerJoin(CatalogueHasImages::class, 'chi', 'WITH', 'chi.image = i AND chi.catalogue = :catalog')
            ->setParameter('catalog', $catalogue);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMainByProduct(Product $product): Image
    {
        $query = $this->createQueryBuilder('i')
            ->innerJoin(ProductHasImages::class, 'phi', 'WITH', 'phi.image = i AND phi.product = :product')
            ->innerJoin(ProductColor::class, 'pc', 'WITH', 'phi.color = pc')
            ->where('i.isMain = :isMain')
            ->setParameter('product', $product)
            ->setParameter('isMain', true)
            ->orderBy('pc.id');

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getRelatedImage(string $imageName): ?Image
    {
        $query = $this->createQueryBuilder('i')
            ->where('i.parentImage = :parentImageName')
            ->setParameter('parentImageName', $imageName);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getFirstByColorAndProduct(Product $product, ProductColor $color): Image
    {
        $query = $this->createQueryBuilder('i')
            ->select(
                'i'
            )
            ->innerJoin(ProductHasImages::class, 'phi', 'WITH', 'phi.image = i AND phi.product = :product')
            ->innerJoin(ProductColor::class, 'pc', 'WITH', 'phi.color = pc')
            ->innerJoin('phi.color', 'color')
            ->where('color = :color')
            ->setParameter('product', $product)
            ->setParameter('color', $color)
            ->setMaxResults(1);

        return $query->getQuery()->getOneOrNullResult();
    }
}
