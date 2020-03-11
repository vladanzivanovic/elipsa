<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Entity\ProductColor;
use App\Entity\ProductSize;
use App\Repository\CategoryRepository;
use App\Repository\ProductColorRepository;
use App\Repository\ProductSizeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class SizeEditPageController extends AbstractController
{
    /**
     * @var ProductSizeRepository
     */
    private $sizeRepository;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * SizeEditPageController constructor.
     *
     * @param ProductSizeRepository $sizeRepository
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        ProductSizeRepository $sizeRepository,
        ParameterBagInterface $bag
    ) {
        $this->sizeRepository = $sizeRepository;
        $this->bag = $bag;
    }

    /**
     * @Route("/add-size", name="admin.add_size_page", methods={"GET"})
     * @Template("Admin/Pages/sizeEdit.html.twig")
     *
     * @return array
     */
    public function insert(): array
    {
        return [];
    }

    /**
     * @Route("/edit-size/{slug}", name="admin.edit_size_page", methods={"GET"})
     * @Template("Admin/Pages/sizeEdit.html.twig")
     *
     * @param ProductSize $productSize
     *
     * @return array
     */
    public function update(ProductSize $productSize): array
    {
        return [
            'size' => $productSize,
        ];
    }
}