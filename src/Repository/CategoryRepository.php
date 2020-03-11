<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
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
}
