<?php

namespace App\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

/**
 * Class ExtendedEntityRepository
 */
class ExtendedEntityRepository extends ServiceEntityRepository
{
    /**
     * @param object $object
     *
     * @throws ORMException
     */
    public function persist($object): void
    {
        $this->_em->persist($object);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush(): void
    {
        $this->_em->flush();
    }

    /**
     * @param $object
     *
     * @throws ORMException
     */
    public function refresh($object): void
    {
        $this->_em->refresh($object);
    }

    /**
     * @param $object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save($object): void
    {
        $this->persist($object);
        $this->flush();
    }

    /**
     * @param $object
     *
     * @throws ORMException
     */
    public function delete($object): void
    {
        $this->_em->remove($object);
    }

    /**
     * @param $object
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function removeWithFlush($object): void
    {
        $this->delete($object);
        $this->flush();
    }
}
