<?php

namespace App\Repository;

use App\Entity\SliderTextTranslation;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SliderTextTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method SliderTextTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method SliderTextTranslation[]    findAll()
 * @method SliderTextTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SliderTextTranslationRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SliderTextTranslation::class);
    }
}
