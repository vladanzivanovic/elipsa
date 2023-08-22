<?php

namespace App\Repository;

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
    public function __construct(ManagerRegistry $registry)
    {
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

    /**
     * @return array
     */
    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('st')
            ->select(
                'st.id as id',
                'st.isActive as is_active',
                'st.position as position',
                'stt.title as title',
                'stt.link as link',
            )
            ->innerJoin('st.sliderTextTranslations', 'stt')
            ->where('stt.locale = \'rs\'')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function getListByPosition(string $position): array
    {
        $query = $this->createQueryBuilder('st')
            ->where('st.isActive = :activeSlider')
            ->andWhere('st.position = :position')
            ->setParameter('activeSlider', SliderText::STATUS_ACTIVE)
            ->setParameter('position', $position)
        ;

        return $query->getQuery()->getResult();
    }
}
