<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\CategoryTranslation;
use App\Formatter\Admin\CategoryEditFormatter;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryEditPageController extends AbstractController
{
    public function __construct(
        private readonly CategoryEditFormatter $categoryEditFormatter,
    ) {}

    #[Route(path: '/add-category', name: 'admin.add_category_page', methods: ['GET'])]
    #[Template('Admin/Pages/categoryEdit.html.twig')]
    public function insert(): array
    {
        return $this->categoryEditFormatter->format();
    }

    
    #[Route(path: '/edit-category/{slug}', name: 'admin.edit_category_page', methods: ['GET'])]
    #[Template('Admin/Pages/categoryEdit.html.twig')]
    public function update(CategoryTranslation $categoryTranslation): array
    {
        return $this->categoryEditFormatter->format($categoryTranslation->getCategory());
    }
}
