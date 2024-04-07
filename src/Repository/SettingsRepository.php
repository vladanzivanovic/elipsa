<?php

namespace App\Repository;

use App\Entity\Settings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getSettingsForOrderEmail(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                's.slug',
                's.value'
            )
            ->where('s.slug IN (:settingsSlug)')
            ->setParameter('settingsSlug', ['MAIN_EMAIL', 'TELEPHONE', 'STREET', 'CITY', 'ZIP_CODE', 'ACCOUNT_NUMBER', 'PIB', 'SHIPPING_PRICE', 'FREE_SHIPPING', 'SITE_NAME']);

        return $query->getQuery()->getArrayResult();
    }

    public function getSettingsForContactPage(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                's.slug',
                's.value',
            )
            ->where('s.slug IN (:settingsSlug)')
            ->setParameter('settingsSlug', ['MAIN_EMAIL', 'TELEPHONE', 'STREET', 'CITY', 'ZIP_CODE', 'ACCOUNT_NUMBER', 'PIB', 'SHIPPING_PRICE', 'FREE_SHIPPING', 'SITE_NAME', 'FULL_COMPANY_NAME', 'COMPANY_ACTIVITY', 'COMPANY_CODE', 'COMPANY_ID', 'FOOTER_BOTTOM_TEXT']);

        return $query->getQuery()->getArrayResult();
    }

    public function getAllSettingsByLocale(string $locale): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                's.slug',
                's.value',
            )
            ->where('s.locale = :locale')
            ->orWhere('s.locale IS NULL')
            ->setParameter('locale', $locale);

        return $query->getQuery()->getArrayResult();
    }

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
