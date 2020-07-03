<?php

namespace App\Repository;

use App\Entity\SliderText;
use App\Model\DataTableModel;
use Doctrine\Common\Persistence\ManagerRegistry;
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
     * @param DataTableModel $tableModel
     *
     * @return array
     */
    public function getAdminList(DataTableModel $tableModel): array
    {
        $query = $this->createQueryBuilder('st')
            ->select(
                'st.id',
                'stt.description',
                'st.isActive as is_active',
                'stt.link'
            )
            ->innerJoin('st.sliderTextTranslations', 'stt')
            ->where('stt.locale = \'rs\'')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('st.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $locale
     *
     * @return array
     */
    public function getList(string $locale): array
    {
        $query = $this->createQueryBuilder('st')
            ->select(
                'stt.description',
                'stt.link'
            )
            ->innerJoin('st.sliderTextTranslations', 'stt')
            ->where('stt.locale = :locale')
            ->andWhere('st.isActive = :activeSlider')
            ->setParameter('locale', $locale)
            ->setParameter('activeSlider', SliderText::STATUS_ACTIVE)
        ;

        return $query->getQuery()->getArrayResult();
    }
}
