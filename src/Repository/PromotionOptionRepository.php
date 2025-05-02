<?php

namespace App\Repository;

use App\Entity\PromotionOption;
use App\Entity\Resources\StatusInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class PromotionOptionRepository extends ExtendedEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly RequestStack $requestStack
    ) {
        parent::__construct($registry, PromotionOption::class);
    }

    public function add(PromotionOption $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PromotionOption $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getActivePromotionsBar(): PromotionOption|null
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $countryCode = $this->requestStack->getCurrentRequest()->attributes->get('_country');

        $query = $this->createQueryBuilder('po')
            ->select('po')
            ->innerJoin('po.promotionId', 'p')
            ->where('p.validTo >= :now')
            ->andWhere('p.validFrom <= :now')
            ->andWhere('po.type = :optionType')
            ->andWhere('p.availableCountries LIKE :countryCode')
            ->andWhere('p.status = :status')
            ->andWhere('po.configuration LIKE :configuration')
            ->setParameter('now', $now)
            ->setParameter('countryCode', '%'.$countryCode.'%')
            ->setParameter('status', StatusInterface::STATUS_ACTIVE)
            ->setParameter('optionType', PromotionOption::OPTION_HOME_SCREEN_BAR)
            ->setParameter('configuration', '%true%')
            ->setMaxResults(1)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }
}
