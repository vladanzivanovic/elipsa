<?php

namespace App\Repository;

use App\Entity\CareerDescription;
use App\Model\DataTableModel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method CareerDescription|null find($id, $lockMode = null, $lockVersion = null)
 * @method CareerDescription|null findOneBy(array $criteria, array $orderBy = null)
 * @method CareerDescription[]    findAll()
 * @method CareerDescription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CareerDescriptionRepository extends ExtendedEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly string $defaultLocale,
    ) {
        parent::__construct($registry, CareerDescription::class);
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

    
    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('c')
            ->select(
                'c.id as id',
                'c.status as status',
                'cdt.title as title',
                'image.name',
                'c.availableCountries as available_countries'

            )
            ->innerJoin('c.image', 'image')
            ->innerJoin('c.translations', 'cdt')
            ->where('cdt.locale = :defaultLocale')
            ->setParameter('defaultLocale', $this->defaultLocale)
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    
    public function getActiveList(string $locale): array
    {
        $query = $this->createQueryBuilder('c')
            ->select(
                'c.id as id',
                'c.status as status',
                'cdt.title as title',
                'cdt.slug as slug',
                'image.name as imageName'
            )
            ->innerJoin('c.careerDescriptionTranslations', 'cdt')
            ->innerJoin('c.image', 'image')
            ->where('c.status = :status')
            ->andWhere('cdt.locale = :locale')
            ->setParameter('status', CareerDescription::STATUS_ACTIVE)
            ->setParameter('locale', $locale);

        return $query->getQuery()->getArrayResult();
    }

    
    public function getForOptions(string $locale): array
    {
        $query = $this->createQueryBuilder('c')
            ->select(
                'c.id as value',
                'cdt.title'
            )
            ->innerJoin('c.careerDescriptionTranslations', 'cdt')
            ->where('cdt.locale = :locale')
            ->andWhere('c.status = :activeStatus')
            ->setParameter('locale', $locale)
            ->setParameter('activeStatus', CareerDescription::STATUS_ACTIVE);

        return $query->getQuery()->getArrayResult();
    }
}
