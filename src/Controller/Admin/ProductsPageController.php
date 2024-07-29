<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Formatter\Admin\ProductsPageResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class ProductsPageController extends AbstractController
{
    public function __construct(
        private readonly ProductsPageResponseFormatter $productsPageResponseFormatter
    ) {
    }

    #[Route(path: '/', name: 'admin.dashboard', methods: ['GET'])]
    #[Template('Admin/Pages/dashboard.html.twig')]
    public function index(): array
    {
        return $this->productsPageResponseFormatter->format();
    }
}
