<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ExtendedEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getByValues(
        string $type,
        string $email,
        array $payload
    ): ?Notification {
        $payload = json_encode($payload);

        $query = $this->createQueryBuilder('n')
            ->where('n.email = :email')
            ->andWhere('n.type = :type')
            ->andWhere('n.payload = :payload')
            ->setParameter('email', $email)
            ->setParameter('type', $type)
            ->setParameter('payload', $payload);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @return array<int, Notification>
     */
    public function getSizeAvailableNotifications(
        string $type,
        int $productId
    ): array {
        $query = $this->createQueryBuilder('n')
            ->where('n.type = :type')
            ->andWhere('JSON_EXTRACT(n.payload, \'$.product\') = :productId')
            ->setParameter('type', $type)
            ->setParameter('productId', $productId);

        return $query->getQuery()->getResult();
    }
}
