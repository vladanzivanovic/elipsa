<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Entity\ProductColor;
use App\Repository\CategoryRepository;
use App\Repository\ProductColorRepository;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryEditPageController extends AbstractController
{
    private \App\Repository\CategoryRepository $categoryRepository;

    /**
     * CategoryEditPageController constructor.
     *
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    #[Route(path: '/add-category', name: 'admin.add_category_page', methods: ['GET'])]
    #[Template('Admin/Pages/categoryEdit.html.twig')]
    public function insert(): array
    {
        return [
            'options' => $this->categoryRepository->getAll(),
        ];
    }

    
    #[Route(path: '/edit-cateogry/{slug}', name: 'admin.edit_category_page', methods: ['GET'])]
    #[Template('Admin/Pages/categoryEdit.html.twig')]
    public function update(CategoryTranslation $categoryTranslation): array
    {
        $category = $categoryTranslation->getCategory();
        return [
            'category' => $category,
            'options' => $this->categoryRepository->getAll($category),
        ];
    }
}
