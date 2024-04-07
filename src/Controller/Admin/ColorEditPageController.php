<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ProductColor;
use App\Repository\ProductColorRepository;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Annotation\Route;

final class ColorEditPageController extends AbstractController
{
    private \App\Repository\ProductColorRepository $colorRepository;

    /**
     * ColorEditPageController constructor.
     *
     * @param ParameterBagInterface  $bag
     */
    public function __construct(
        ProductColorRepository $colorRepository
    ) {
        $this->colorRepository = $colorRepository;
    }

    #[Route(path: '/add-color', name: 'admin.add_color_page', methods: ['GET'])]
    #[Template('Admin/Pages/colorEdit.html.twig')]
    public function insert(): array
    {
        return [];
    }

    
    #[Route(path: '/edit-color/{id}', name: 'admin.edit_color_page', methods: ['GET'])]
    #[Template('Admin/Pages/colorEdit.html.twig')]
    public function update(ProductColor $color): array
    {
        $colors = $this->colorRepository->getByColorForAdmin($color);

        return $colors[0];
    }
}
