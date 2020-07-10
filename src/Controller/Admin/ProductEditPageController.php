<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ProductTranslation;
use App\Formatter\Admin\ProductEditResponseFormatter;
use App\Repository\CategoryRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductSizeRepository;
use App\Repository\TagsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ProductEditPageController extends AbstractController
{
    /**
     * @var TagsRepository
     */
    private $tagsRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;
    /**
     * @var ProductColorRepository
     */
    private $colorRepository;
    /**
     * @var ProductEditResponseFormatter
     */
    private $responseFormatter;

    /**
     * ProductEditPageController constructor.
     *
     * @param TagsRepository               $tagsRepository
     * @param CategoryRepository           $categoryRepository
     * @param ProductSizeRepository        $sizeRepository
     * @param ProductColorRepository       $colorRepository
     * @param ProductEditResponseFormatter $responseFormatter
     */
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
            'sizes' => $this->sizeRepository->getForOptions(),
            'colors' => $this->colorRepository->getForOptions(),
        ];
    }

    /**
     * @Route("/edit-product/{slug}", name="admin.edit_product_page", methods={"GET"})
     * @Template("Admin/Pages/productEdit.html.twig")
     *
     * @param ProductTranslation $productTranslation
     *
     * @return array
     */
    public function edit(ProductTranslation $productTranslation)
    {
        $productData = $this->responseFormatter->formatResponse($productTranslation->getProduct());

        $options = [
            'tags' => $this->tagsRepository->getForOptions(),
            'categories' => $this->categoryRepository->getAll(),
            'sizes' => $this->sizeRepository->getForOptions(),
            'colors' => $this->colorRepository->getForOptions(),
        ];

        return  $productData + $options;
    }
}