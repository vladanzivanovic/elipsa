<?php

namespace App\Repository;

use App\Entity\Resources\StatusInterface;
use App\Entity\SliderText;
use App\Model\DataTableModel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method SliderText|null find($id, $lockMode = null, $lockVersion = null)
 * @method SliderText|null findOneBy(array $criteria, array $orderBy = null)
 * @method SliderText[]    findAll()
 * @method SliderText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SliderTextRepository extends ExtendedEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly string $defaultLocale,
    ) {
        parent::__construct($registry, SliderText::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('st')
            ->select('COUNT(st.id) as total')
        ;

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('st')
            ->select(
                'st.id as id',
                'st.status as status',
                'st.position as position',
                'stt.title as title',
                'stt.link as link',
                'st.availableCountries as available_countries',
            )
            ->innerJoin('st.translations', 'stt')
            ->where('stt.locale = :defaultLocale')
            ->setParameter('defaultLocale', $this->defaultLocale)
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function getListByPosition(string $position, string $countryCode): array
    {
        $query = $this->createQueryBuilder('st')
            ->where('st.status = :activeSlider')
            ->andWhere('st.position = :position')
            ->andWhere('st.availableCountries LIKE :country')
            ->setParameter('activeSlider', StatusInterface::STATUS_ACTIVE)
            ->setParameter('position', $position)
            ->setParameter('country', '%'.$countryCode.'%')
        ;

        return $query->getQuery()->getResult();
    }
}
