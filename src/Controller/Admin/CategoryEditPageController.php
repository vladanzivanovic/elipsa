<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Entity\ProductColor;
use App\Repository\CategoryRepository;
use App\Repository\ProductColorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryEditPageController extends AbstractController
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * CategoryEditPageController constructor.
     *
     * @param CategoryRepository    $categoryRepository
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ParameterBagInterface $bag
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-category", name="admin.add_category_page", methods={"GET"})
     * @Template("Admin/Pages/categoryEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [
            'options' => $this->categoryRepository->getAll(),
        ];
    }

    /**
     * @Route("/edit-cateogry/{slug}", name="admin.edit_category_page", methods={"GET"})
     * @Template("Admin/Pages/categoryEdit.html.twig")
     *
     * @param CategoryTranslation $categoryTranslation
     *
     * @return array
     */
    public function update(CategoryTranslation $categoryTranslation): array
    {
        $category = $categoryTranslation->getCategory();
        return [
            'category' => $category,
            'options' => $this->categoryRepository->getAll($category),
        ];
    }
}