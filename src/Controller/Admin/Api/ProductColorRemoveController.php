<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Product;
use App\Entity\ProductColor;
use App\Entity\Resources\StatusInterface;
use App\Exception\GenericTranslationException;
use App\Handler\ProductColorHandler;
use App\Repository\ProductHasImagesRepository;
use App\View\ExceptionView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class ProductColorRemoveController extends AbstractController
{
    public function __construct(
        private readonly ProductColorHandler $colorHandler,
        private readonly ProductHasImagesRepository $hasImagesRepository,
        private readonly ExceptionView $exceptionView,
        private readonly string $adminLocale,
    ) {}

    /**
     *
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/remove-color/{id}', name: 'admin.remove_color_api', options: ['expose' => true], methods: ['DELETE'])]
    public function remove(ProductColor $productColor): JsonResponse
    {
        try {
            $hasActiveProducts = $this->hasImagesRepository->colorsInUseByProductStatus($productColor, Product::STATUS_ACTIVE);

            if ($hasActiveProducts) {
                $trans = $productColor->getByLocale($this->adminLocale);

                $genericException = new GenericTranslationException('error.in_use');
                $genericException->setDomain('messages');
                $genericException->setParameters(['%item%' => $trans->getTitle()]);

                throw $genericException;
            }

            $hasArchivedProducts = $this->hasImagesRepository->colorsInUseByProductStatus($productColor, Product::STATUS_ARCHIVED);
            $hasPendingProducts = $this->hasImagesRepository->colorsInUseByProductStatus($productColor, Product::STATUS_PENDING);


            if ($productColor->isInUseByOrderProduct() || $hasArchivedProducts || $hasPendingProducts) {
                $productColor->setStatus(StatusInterface::STATUS_ARCHIVED);

                $this->colorHandler->save($productColor);

                return $this->json([]);
            }

            $this->colorHandler->remove($productColor);

            return $this->json([]);
        } catch (\Throwable $throwable) {
            return $this->json(
                ['error' => $this->exceptionView->view($throwable, $this->adminLocale)],
                Response::HTTP_BAD_REQUEST
            );
        }
    }
}
