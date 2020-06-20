<?php

namespace App\Repository;

use App\Entity\Image;
use App\Entity\Slider;
use App\Model\DataTableModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * @method Slider|null find($id, $lockMode = null, $lockVersion = null)
 * @method Slider|null findOneBy(array $criteria, array $orderBy = null)
 * @method Slider[]    findAll()
 * @method Slider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SliderRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Slider::class);
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('s')
            ->select('COUNT(s.id) as total')
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
        $query = $this->createQueryBuilder('s')
            ->select(
                's.id',
                's.position',
                's.isActive as is_active',
                'image.name'
            )
            ->innerJoin('s.image', 'image')
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy('s.' . $tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @return array
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getLastPosition(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                's.position'
            )
            ->orderBy('s.position', 'DESC')
            ->setMaxResults(1);

        return $query->getQuery()->getScalarResult();
    }

    /**
     * @param int $position
     *
     * @return array
     */
    public function getHigherThenPosition(int $position): array
    {
        $query = $this->createQueryBuilder('s')
            ->where('s.position > :position')
            ->setParameter('position', $position);

        return $query->getQuery()->getResult();
    }

    /**
     * @param string $locale
     * @param int    $device
     *
     * @return array
     */
    public function getActiveSliderByPosition(string $locale): array
    {
        $query = $this->createQueryBuilder('s')
            ->select(
                's.id',
                's.textPosition as position',
                'st.description',
                'st.buttonText as button_text',
                'st.buttonLink as button_link',
                'i.name as image',
                'mi.name as mobile_image'
            )
            ->innerJoin('s.sliderTranslations', 'st')
            ->innerJoin('s.image', 'i')
            ->innerJoin(Image::class, 'mi', 'WITH', 'mi.parentImage = i.name')
            ->where('s.isActive = :isActive')
            ->andWhere('st.locale = :locale')
            ->setParameter('isActive', true)
            ->setParameter('locale', $locale)
            ->orderBy('s.position');

        return $query->getQuery()->getArrayResult();
    }
}
