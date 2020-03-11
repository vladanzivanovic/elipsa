<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\CategoryTranslation;
use App\Entity\ProductColor;
use App\Handler\CategoryHandler;
use App\Handler\ProductColorHandler;
use App\Repository\ProductHasColorRepository;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryRemoveController extends AbstractController
{
    /**
     * @var CategoryHandler
     */
    private $categoryHandler;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * CategoryRemoveController constructor.
     *
     * @param CategoryHandler   $categoryHandler
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CategoryHandler $categoryHandler,
        ProductRepository $productRepository
    ) {
        $this->categoryHandler = $categoryHandler;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/api/remove-category/{slug}", name="admin.remove_category_api", methods={"DELETE"}, options={"expose": true})
     *
     * @param CategoryTranslation $categoryTranslation
     *
     * @return JsonResponse
     */
    public function remove(CategoryTranslation $categoryTranslation)
    {
        $category = $categoryTranslation->getCategory();
        $productCount = $category->getProducts()->count();

        if ($productCount > 0 || $category->getChildren()->count() > 0) {
            return $this->json(['message' => 'error.in_use'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->categoryHandler->remove($category);

        return $this->json([]);
    }
}