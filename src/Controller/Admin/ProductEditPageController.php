<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ProductTranslation;
use App\Formatter\Admin\ProductEditResponseFormatter;
use App\Repository\CategoryRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ProductEditPageController extends AbstractController
{
    private TagsRepository $tagsRepository;

    private CategoryRepository $categoryRepository;

    private ProductSizeRepository $sizeRepository;

    private ProductColorRepository $colorRepository;

    private ProductEditResponseFormatter $responseFormatter;

    public function __construct(
        TagsRepository $tagsRepository,
        CategoryRepository $categoryRepository,
        ProductSizeRepository $sizeRepository,
        ProductColorRepository $colorRepository,
        ProductEditResponseFormatter $responseFormatter
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->categoryRepository = $categoryRepository;
        $this->sizeRepository = $sizeRepository;
        $this->colorRepository = $colorRepository;
        $this->responseFormatter = $responseFormatter;
    }

    #[Route(path: '/add-product', name: 'admin.add_product_page', methods: ['GET'])]
    #[Template('Admin/Pages/productEdit.html.twig')]
    public function insert(): array
    {
        $options = [
            'tags' => $this->tagsRepository->getForOptions(),
            'categories' => $this->categoryRepository->getAll(),
            'sizes' => $this->sizeRepository->getForOptions(),
            'colors' => $this->colorRepository->getForOptions(),
        ];

        return $this->responseFormatter->formatResponse($options);
    }

    #[Route(path: '/edit-product/{slug}', name: 'admin.edit_product_page', methods: ['GET'])]
    #[Template('Admin/Pages/productEdit.html.twig')]
    public function edit(ProductTranslation $productTranslation): array
    {
        $options = [
            'tags' => $this->tagsRepository->getForOptions(),
            'categories' => $this->categoryRepository->getAll(),
            'sizes' => $this->sizeRepository->getForOptions(),
            'colors' => $this->colorRepository->getForOptions(),
        ];

        return $this->responseFormatter->formatResponse($options, $productTranslation->getProduct());
    }
}
