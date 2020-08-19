<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Catalogue;
use App\Helper\ValidatorHelper;
use App\Repository\CatalogueRepository;
use App\Services\ImageService;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class CatalogHandler
{
    /**
     * @var ImageService
     */
    protected $img;

    /**
     * @var ValidatorHelper
     */
    private $validator;

    /**
     * @var CatalogueRepository
     */
    private $catalogueRepository;

    /**
     * @param ValidatorHelper     $validator
     * @param CatalogueRepository $catalogueRepository
     */
    public function __construct(
        ValidatorHelper $validator,
        CatalogueRepository $catalogueRepository
    ) {
        $this->validator = $validator;
        $this->catalogueRepository = $catalogueRepository;
    }

    /**
     * @param Catalogue $catalogue
     *
     * @return void
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Catalogue $catalogue): void
    {
        $errors = $this->validator->validate($catalogue, null, "SetCatalog");

        if ($errors->count() > 0) {
            throw new UnprocessableEntityHttpException(json_encode($this->validator->parseErrors($errors)));
        }

        if (is_null($catalogue->getId())) {
            $this->catalogueRepository->persist($catalogue);
        }

        $this->catalogueRepository->flush();
    }

    /**
     * @param Catalogue $catalogue
     * @param int       $status
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeStatus(Catalogue $catalogue, int $status): void
    {
        $catalogue->setStatus($status);

        $this->catalogueRepository->flush();
    }

    /**
     * @param Catalogue $catalogue
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function remove(Catalogue $catalogue): void
    {
        $this->catalogueRepository->delete($catalogue);
        $this->catalogueRepository->flush();
    }
}