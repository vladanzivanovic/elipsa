<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\ProductColor;
use App\Handler\ProductColorHandler;
use App\Repository\ProductHasColorRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class ProductColorRemoveController extends AbstractController
{
    /**
     * @var ProductColorHandler
     */
    private $colorHandler;
    /**
     * @var ProductHasColorRepository
     */
    private $hasColorRepository;

    /**
     * ProductColorRemoveController constructor.
     *
     * @param ProductColorHandler       $colorHandler
     * @param ProductHasColorRepository $hasColorRepository
     */
    public function __construct(
        ProductColorHandler $colorHandler,
        ProductHasColorRepository $hasColorRepository
    ) {
        $this->colorHandler = $colorHandler;
        $this->hasColorRepository = $hasColorRepository;
    }

    /**
     * @Route("/api/remove-color/{slug}", name="admin.remove_color_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param ProductColor $productColor
     *
     * @return JsonResponse
     */
    public function remove(ProductColor $productColor)
    {
        $mainSlug = $productColor->getMainSlug();

        $productCount = $this->hasColorRepository->count(['color' => $mainSlug]);

        if ($productCount > 0) {
            throw new BadRequestHttpException(json_encode(['message' => 'error.in_use']));
        }

        $this->colorHandler->remove($mainSlug);

        return $this->json([]);
    }
}