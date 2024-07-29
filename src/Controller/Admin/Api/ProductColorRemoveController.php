<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\ProductColor;
use App\Handler\ProductColorHandler;
use App\Repository\ProductHasColorRepository;
use App\Repository\ProductHasImagesRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class ProductColorRemoveController extends AbstractController
{
    private \App\Handler\ProductColorHandler $colorHandler;

    private \App\Repository\ProductHasImagesRepository $hasImagesRepository;

    /**
     * ProductColorRemoveController constructor.
     */
    public function __construct(
        ProductColorHandler $colorHandler,
        ProductHasImagesRepository $hasImagesRepository
    ) {
        $this->colorHandler = $colorHandler;
        $this->hasImagesRepository = $hasImagesRepository;
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    #[Route(path: '/api/remove-color/{id}', name: 'admin.remove_color_api', methods: ['DELETE'], options: ['expose' => true])]
    public function remove(ProductColor $productColor)
    {
        $productCount = $this->hasImagesRepository->count(['color' => $productColor]);

        if ($productCount > 0) {
            throw new BadRequestHttpException(json_encode(['message' => 'error.in_use']));
        }

        $this->colorHandler->remove($productColor);

        return $this->json([]);
    }
}
