<?php

namespace App\Repository;

use App\Entity\CategoryTranslation;
use App\Entity\Product;
use App\Entity\ProductHasCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CategoryTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryTranslation[]    findAll()
 * @method CategoryTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryTranslationRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryTranslation::class);
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getByProduct(Product $product): array
    {
        $query = $this->createQueryBuilder('ct')
            ->select(
                'ct.slug'
            )
            ->innerJoin('ct.category', 'c')
            ->innerJoin(ProductHasCategories::class, 'phc', 'WITH', 'phc.category = c AND phc.product = :product')
            ->where('ct.locale = \'rs\'')
            ->setParameter('product', $product);

        return $query->getQuery()->getArrayResult();
    }
}
