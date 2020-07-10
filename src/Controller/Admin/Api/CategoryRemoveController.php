<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\CategoryTranslation;
use App\Handler\CategoryHandler;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param CategoryHandler     $categoryHandler
     * @param ProductRepository   $productRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        CategoryHandler $categoryHandler,
        ProductRepository $productRepository,
        TranslatorInterface $translator
    ) {
        $this->categoryHandler = $categoryHandler;
        $this->productRepository = $productRepository;
        $this->translator = $translator;
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
        $productCount = $category->getProductHasCategories()->count();

        if ($productCount > 0 || $category->getChildren()->count() > 0) {
            return $this->json(['message' => $this->translator->trans('error.in_use', ['%item%' => 'Kategorija'])], JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->categoryHandler->remove($category);

        return $this->json([]);
    }
}