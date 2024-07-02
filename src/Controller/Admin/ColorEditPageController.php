<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ProductColor;
use App\Formatter\Admin\ColorEditResponseFormatter;
use App\Repository\ProductColorRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class ColorEditPageController extends AbstractController
{
    public function __construct(
        private readonly ColorEditResponseFormatter $responseFormatter,
    ) {}

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
        return $this->responseFormatter->formatResponse($color);
    }
}
