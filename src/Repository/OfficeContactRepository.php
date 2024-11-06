<?php

namespace App\Repository;

use App\Entity\OfficeContact;
use App\Entity\OfficeContactTranslation;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OfficeContact>
 *
 * @method OfficeContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method OfficeContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method OfficeContact[]    findAll()
 * @method OfficeContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OfficeContactRepository extends ExtendedEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly array $countries
    ) {
        parent::__construct($registry, OfficeContact::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('oc')
            ->select('COUNT(oc.id) as total')
        ;

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('oc')
            ->select(
                'oc.id as id',
                'oc.telephone as telephone',
                'oc.showInFooter as isShownInFooter',
                'oct.title as title',
                'oc.availableCountries as available_countries'
            )
            ->innerJoin(OfficeContactTranslation::class, 'oct', 'WITH', 'oct.officeContact = oc AND oct.locale IN (:countries)')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->groupBy('oc.id')
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        foreach ($this->countries as $countryCode => $country) {
            $countries[] = $countryCode;
        }

        $query->setParameter('countries', $countries);

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return array<int, OfficeContact>
     */
    public function getFooterContacts(string $countryCode): array
    {
        $query = $this->createQueryBuilder('oc')
            ->where('oc.showInFooter = true')
            ->andWhere('oc.availableCountries LIKE :countryCode')
            ->setParameter('countryCode', '%' . $countryCode . '%');

        return $query->getQuery()->getResult();
    }

    /**
     * @return array<int, OfficeContact>
     */
    public function getContactsByFields(array $fields, string $countryCode): array
    {
        $query = $this->createQueryBuilder('oc');

        $query->where($query->expr()->like('oc.availableCountries', ':countryCode'))
            ->setParameter('countryCode', '%' . $countryCode . '%');

        $orWhereFields = [];

        foreach ($fields as $field => $value) {
            $orWhereFields[] = 'oc.'. $field .'='. $value;
        }

        $query->andWhere($query->expr()->orX(
            ...$orWhereFields
        ));

        return $query->getQuery()->getResult();
    }
}
