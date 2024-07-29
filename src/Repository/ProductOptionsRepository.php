<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductOptions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Country;

/**
 * @extends ServiceEntityRepository<ProductOptions>
 *
 * @method ProductOptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductOptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductOptions[]    findAll()
 * @method ProductOptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductOptionsRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductOptions::class);
    }

    public function getLowestAndHighestPrice(string $country): array
    {
        $query = $this->createQueryBuilder('po')
            ->select(
                'MIN (po.price) as lowPrice',
                'MAX (po.price) as highPrice'
            )
            ->innerJoin('po.product', 'p')
            ->where('p.status = :status')
            ->andWhere('po.country = :country')
            ->setParameter('status', Product::STATUS_ACTIVE)
            ->setParameter('country', $country);

        return $query->getQuery()->getArrayResult();
    }

    public function getHighestHomePagePosition(
        string $homePagePosition,
        string $country
    ): null|array {
        $query = $this->createQueryBuilder('po')
            ->select(
                'po.showHomePage'
            )
            ->innerJoin('po.product', 'p')
            ->where('p.status = :status')
            ->andWhere('po.showHomePage LIKE :homePagePositionKey')
            ->andWhere('po.country = :country')
            ->setParameter('status', Product::STATUS_ACTIVE)
            ->setParameter('homePagePositionKey', '%"'.$homePagePosition.'":%')
            ->setParameter('country', $country)
            ->orderBy('po.showHomePage', 'DESC')
            ->setMaxResults(1);

        return $query->getQuery()->getOneOrNullResult();
    }

    public function getProductsHasHomePagePosition(
        string $homePagePosition,
        string $country
    ): null|array {
        $query = $this->createQueryBuilder('po')
            ->andWhere('po.showHomePage LIKE :homePagePositionKey')
            ->andWhere('po.country = :country')
            ->setParameter('homePagePositionKey', '%"'.$homePagePosition.'":%')
            ->setParameter('country', $country)
            ->orderBy('CAST(JSON_EXTRACT(po.showHomePage, \'$.'.$homePagePosition.'\') AS UNSIGNED)', 'ASC');

        return $query->getQuery()->getResult();
    }
}
