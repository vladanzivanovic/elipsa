<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserWishes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\ParameterType;

/**
 * @method UserWishes|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWishes|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWishes[]    findAll()
 * @method UserWishes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserWishesRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWishes::class);
    }

    /**
     * @param User   $user
     * @param string $locale
     *
     * @return array
     */
    public function getByUser(User $user, string $locale): array
    {
        $query = $this->createQueryBuilder('uw')
            ->select(
                'p.id',
                'p.price',
                'p.discount',
                'pt.slug',
                'pt.shortDescription as short_description',
                'pt.title',
                'i.name as image_name'
            )
            ->innerJoin('uw.product', 'p')
            ->innerJoin('p.productTranslations', 'pt')
            ->innerJoin('p.productHasImages', 'phi')
            ->innerJoin('phi.image', 'i')
            ->where('uw.user = :user')
            ->andWhere('p.status = :activeStatus')
            ->andWhere('pt.locale = :locale')
            ->andWhere('i.isMain = :isMain')
            ->setParameter('user', $user)
            ->setParameter('activeStatus', Product::STATUS_ACTIVE, ParameterType::INTEGER)
            ->setParameter('locale', $locale, ParameterType::STRING)
            ->setParameter('isMain', true, ParameterType::BOOLEAN)
            ->orderBy('uw.id', 'DESC')
            ->groupBy('p.id');

        return $query->getQuery()->getArrayResult();
    }
}
