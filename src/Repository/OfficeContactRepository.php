<?php

namespace App\Repository;

use App\Entity\OfficeContact;
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
    public function __construct(ManagerRegistry $registry)
    {
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
                'oct.title as title'
            )
            ->innerJoin('oc.officeContactTranslations', 'oct')
            ->where('oct.locale = \'rs\'')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return array<int, OfficeContact>
     */
    public function getFooterContacts(): array
    {
        $query = $this->createQueryBuilder('oc')
            ->where('oc.showInFooter = true');

        return $query->getQuery()->getResult();
    }

    /**
     * @return array<int, OfficeContact>
     */
    public function getContactsByFields(array $fields): array
    {
        $query = $this->createQueryBuilder('oc');

        foreach ($fields as $field => $value) {
            $query->orWhere('oc.'. $field .' = '. $value);
        }

        return $query->getQuery()->getResult();
    }
}
