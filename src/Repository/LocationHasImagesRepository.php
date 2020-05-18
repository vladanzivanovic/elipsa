<?php

namespace App\Repository;

use App\Entity\LocationHasImages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method LocationHasImages|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocationHasImages|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocationHasImages[]    findAll()
 * @method LocationHasImages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationHasImagesRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationHasImages::class);
    }
}
