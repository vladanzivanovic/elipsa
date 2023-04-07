<?php

namespace App\Repository;

use App\Entity\BlogHasImages;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LocationHasImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocationHasImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocationHasImages[]    findAll()
 * @method LocationHasImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogHasImagesRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogHasImages::class);
    }
}
