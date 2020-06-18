<?php

namespace App\Repository;

use App\Entity\Settings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Settings|null find($id, $lockMode = null, $lockVersion = null)
 * @method Settings|null findOneBy(array $criteria, array $orderBy = null)
 * @method Settings[]    findAll()
 * @method Settings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingsRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Settings::class);
    }

    /**
     * @return array
     */
    public function getSettingsForOrderEmail(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                's.slug',
                's.value'
            )
            ->where('s.slug IN (:settingsSlug)')
            ->setParameter('settingsSlug', ['MAIN_EMAIL', 'TELEPHONE', 'MOBILE_PHONE', 'STREET', 'CITY', 'ZIP_CODE', 'ACCOUNT_NUMBER', 'PIB', 'SHIPPING_PRICE', 'FREE_SHIPPING', 'SITE_NAME']);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
    public function getSettingsForUserRegistrationEmail(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                's.slug',
                's.value'
            )
            ->where('s.slug IN (:settingsSlug)')
            ->setParameter('settingsSlug', ['MAIN_EMAIL', 'SITE_NAME']);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return array
     */
    public function getEmailSettings(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                's.slug',
                's.value'
            )
            ->where('s.slug IN (:settingsSlug)')
            ->setParameter('settingsSlug', ['MAIN_EMAIL']);

        return $query->getQuery()->getArrayResult();
    }
}
