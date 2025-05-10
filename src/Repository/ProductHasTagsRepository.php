<?php

namespace App\Repository;

use App\Entity\ProductHasTags;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductHasTags|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductHasTags|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductHasTags[]    findAll()
 * @method ProductHasTags[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductHasTagsRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductHasTags::class);
    }

    public function getProductsByTags(array $tags): array
    {
        $query = $this->createQueryBuilder('pht')
            ->select('pht.product')
            ->where('pht.tag IN (:tags)')
            ->setParameter('tags', $tags)
        ;

        return $query->getQuery()->getResult();
    }
}
