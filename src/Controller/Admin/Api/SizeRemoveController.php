<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Product;
use App\Entity\ProductSize;;

use App\Entity\Resources\StatusInterface;
use App\Exception\GenericTranslationException;
use App\Handler\SizeHandler;
use App\Repository\ProductHasSizesRepository;
use App\View\ExceptionView;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SizeRemoveController extends AbstractController
{
    public function __construct(
        private readonly SizeHandler $sizeHandler,
        private readonly ProductHasSizesRepository $productHasSizesRepository,
        private readonly ExceptionView $exceptionView,
        private readonly string $adminLocale,
    ) {}

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/remove-size/{id}', name: 'admin.remove_size_api', options: ['expose' => true], methods: ['DELETE'])]
    public function remove(ProductSize $size): JsonResponse
    {
        try {
            $existsInActiveProducts = $this->productHasSizesRepository->sizeExistsInProductsByStatus($size, Product::STATUS_ACTIVE);

            if ($existsInActiveProducts) {

                $genericException = new GenericTranslationException('error.in_use');
                $genericException->setDomain('messages');
                $genericException->setParameters(['%item%' => $size->getSize()]);

                throw $genericException;
            }

            $existsInArchivedProducts = $this->productHasSizesRepository->sizeExistsInProductsByStatus($size, Product::STATUS_ARCHIVED);
            $existsInPendingProducts = $this->productHasSizesRepository->sizeExistsInProductsByStatus($size, Product::STATUS_PENDING);

            if ($existsInArchivedProducts || $existsInPendingProducts) {
                $size->setStatus(StatusInterface::STATUS_ARCHIVED);

                $this->sizeHandler->save($size);

                return $this->json([]);
            }

            $this->sizeHandler->remove($size);

            return $this->json([]);

        } catch (GenericTranslationException $throwable) {
            return $this->json(
                ['error' => $this->exceptionView->view($throwable, $this->adminLocale)],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
