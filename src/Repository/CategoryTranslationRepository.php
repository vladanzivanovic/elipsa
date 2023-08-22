<?php

namespace App\Repository;

use App\Entity\CategoryTranslation;
use App\Entity\ColorTranslation;
use App\Entity\Product;
use App\Entity\ProductHasCategories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

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

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return int|mixed|string
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getForLocalization(string $slug, string $locale)
    {
        $query = $this->createQueryBuilder('ct')
            ->select(
                'ctt.slug'
            )
            ->innerJoin(CategoryTranslation::class, 'ctt', 'WITH', 'ctt.category = ct.category')
            ->where('ct.slug = :slug')
            ->andWhere('ctt.locale = :locale')
            ->setParameter('slug', $slug)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getSingleScalarResult();
    }
}
