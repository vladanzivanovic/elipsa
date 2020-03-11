<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\ProductTagsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ProductEditPageController extends AbstractController
{
    /**
     * @var ProductTagsRepository
     */
    private $tagsRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * ProductEditPageController constructor.
     *
     * @param ProductTagsRepository $tagsRepository
     * @param CategoryRepository    $categoryRepository
     */
    public function __construct(
        ProductTagsRepository $tagsRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->tagsRepository = $tagsRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/add-product", name="admin.add_product_page", methods={"GET"})
     * @Template("Admin/Pages/productEdit.html.twig")
     *
     * @return array
     */
    public function insert()
    {
        return [
            'tags' => $this->tagsRepository->getForOptions(),
            'categories' => $this->categoryRepository->getAll(),
        ];
    }
}