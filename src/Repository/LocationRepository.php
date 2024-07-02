<?php

namespace App\Repository;

use App\Entity\Location;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ExtendedEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly TranslatorInterface $translator,
        private readonly array $countries,
    ) {
        parent::__construct($registry, Location::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('l')
            ->select('COUNT(l.id) as total')
        ;

        return $query->getQuery()->getSingleScalarResult();
    }
    
    public function getList(string $locale): array
    {
        $query = $this->getDqlForList($locale)
            ->addSelect(
                'l.lat',
                'l.lng',
                'GROUP_CONCAT(image.name) as images',
                'lt.shortDescription as short_description',
                'lt.country'
            )
            ->innerJoin('l.locationHasImages', 'lhi')
            ->innerJoin('lhi.image', 'image')
            ->groupBy('l.id');

        return $query->getQuery()->getArrayResult();
    }

    
    public function getCountryList(string $locale): array
    {
        $query = $this->createQueryBuilder('l')
            ->select(
                'DISTINCT(l.countryCode) as country_code',
                'lt.country as name'
            )
            ->innerJoin('l.locationTranslations', 'lt')
            ->where('lt.locale = :locale')
            ->setParameter('locale', $locale);

        return $query->getQuery()->getArrayResult();
    }

    
    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->getDqlForList('rs')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return array<int, Location>
     */
    public function getForOptions(string $locale = 'rs'): array
    {
        $query = $this->createQueryBuilder('l')
            ->innerJoin('l.locationTranslations', 'lt')
            ->where('lt.locale = :locale')
            ->setParameter('locale', $locale);

        return $query->getQuery()->getResult();
    }

    
    private function getDqlForList(string $locale): QueryBuilder
    {
        return $this->createQueryBuilder('l')
            ->select(
                'l.id as id',
                'lt.title as title',
                'lt.slug',
                'CONCAT(lt.street, \',\', l.zipCode, \' \', lt.city, \' \', lt.country) as address',
                'l.telephone as telephone',
                'l.email as email',
                'l.workingTime as working_time',
                'l.saturday as saturday',
                'l.sunday as sunday'
            )
            ->innerJoin('l.locationTranslations', 'lt')
            ->where('lt.locale = :locale')
            ->setParameter('locale', $locale)
        ;
    }
}
