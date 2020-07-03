<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Entity\Product;
use App\Model\DataTableModel;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as total')
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
        $query = $this->createQueryBuilder('c')
            ->select(
                'c.id',
                'GROUP_CONCAT(ct.title) as titles',
                'GROUP_CONCAT(ct.locale) as locales',
                'IFELSE(ct.locale = \'rs\', ct.slug, NULL) slug',
                'ctparent.title as parent'
            )
            ->innerJoin(CategoryTranslation::class, 'ct', 'WITH', 'ct.category = c')
            ->leftJoin(CategoryTranslation::class, 'ctparent', 'WITH', 'ctparent.category = c.parent AND ctparent.locale = \'rs\'')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('c.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection())
            ->groupBy('c.id')
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param Category $category
     *
     * @return array
     */
    public function getAll(?Category $category = null): array
    {
        $query = $this->createQueryBuilder('c')
            ->select(
                'ct.title',
                'ct.slug as value'
            )
            ->innerJoin(CategoryTranslation::class, 'ct', 'WITH', 'ct.category = c AND ct.locale = \'rs\'');

        if ($category instanceof Category) {
            $query->where('c != :category')
                ->setParameter('category', $category);
        }

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $locale
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getForNavigationMenu(string $locale): array
    {
        $sql = "with RECURSIVE cte AS (
                    SELECT c.id, c.parent_id, ct.title, ct.slug, 1 AS lvl
                    FROM category as c
                    INNER JOIN category_translation ct ON c.id = ct.category_id
                    WHERE parent_id IS NULL AND
                          ct.locale = :locale
                    UNION ALL
                    SELECT c2.id, c2.parent_id, t.title, t.slug, lvl + 1
                    FROM category as c2
                    INNER JOIN cte ON cte.id = c2.parent_id
                    INNER JOIN category_translation t ON c2.id = t.category_id
                    WHERE t.locale = :locale
                )
                SELECT * FROM cte order by lvl;";

        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute(['locale' => $locale]);

        return $stmt->fetchAll();
    }

    /**
     * @param Product $product
     * @param string  $locale
     *
     * @return array
     */
    public function getByProduct(Product $product, string $locale): array
    {
        $query = $this->createQueryBuilder('c')
            ->select(
                'ct.title',
                'ct.slug'
            )
            ->innerJoin('c.categoryTranslations', 'ct')
            ->innerJoin('c.productHasCategories', 'phc')
            ->where('ct.locale = :locale')
            ->andWhere('phc.product = :product')
            ->setParameter('locale', $locale)
            ->setParameter('product', $product);

        return $query->getQuery()->getArrayResult();
    }
}
