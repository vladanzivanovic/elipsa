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
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class SizeEditPageController extends AbstractController
{
    /**
     * SizeEditPageController constructor.
     *
     * @param ProductSizeRepository $sizeRepository
     * @param ParameterBagInterface $bag
     */
    public function __construct()
    {
    }

    #[Route(path: '/add-size', name: 'admin.add_size_page', methods: ['GET'])]
    #[Template('Admin/Pages/sizeEdit.html.twig')]
    public function insert(): array
    {
        return [];
    }

    
    #[Route(path: '/edit-size/{slug}', name: 'admin.edit_size_page', methods: ['GET'])]
    #[Template('Admin/Pages/sizeEdit.html.twig')]
    public function update(ProductSize $productSize): array
    {
        return [
            'size' => $productSize,
        ];
    }
}
