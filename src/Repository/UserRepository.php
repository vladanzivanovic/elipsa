<?php

namespace App\Repository;

use App\Entity\User;
use App\Model\DataTableModel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ExtendedEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param UserInterface $user
     * @param string        $newEncodedPassword
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @param string    $email
     * @param User|null $user
     *
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countByEmail(string $email, User $user = null): int
    {
        $query = $this->createQueryBuilder('u')
            ->select('COUNT(u.id) as total')
            ->where('u.email = :email')
            ->setParameter('email', $email);

        if (null !== $user) {
            $query->andWhere('u != :user')
                ->setParameter('user', $user);
        }

        return (int) $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @return mixed
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countData()
    {
        $query = $this->createQueryBuilder('u')
            ->select('COUNT(u.id) as total')
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
        $query = $this->createQueryBuilder('u')
            ->select(
                'u.id as id',
                'CONCAT(u.firstName, \' \', u.lastName) as full_name',
                'u.email as email',
                'u.status as status',
                'u.roles as roles'
            )
            ->setFirstResult($tableModel->getOffset())
            ->setMaxResults($tableModel->getLimit())
            ->orderBy($tableModel->getOrderColumn(), $tableModel->getOrderDirection())
        ;

        return $query->getQuery()->getArrayResult();
    }

    /**
     * @param string $email
     *
     * @return User|null
     * @throws NonUniqueResultException
     */
    public function getByEmail(string $email): ?User
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.email = :email')
            ->setParameter('email', $email);

        return $query->getQuery()->getOneOrNullResult();
    }
}
