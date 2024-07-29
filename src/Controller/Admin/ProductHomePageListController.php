<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Collector\Admin\ProductHomePageCollector;
use App\Formatter\Admin\ProductHomePageFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ProductHomePageListController extends AbstractController
{
    public function __construct(
        private readonly ProductHomePageCollector $productHomePageCollector,
        private readonly ProductHomePageFormatter $productHomePageFormatter,
    ) {}

    #[Route(path: '/products/home-page', name: 'admin.get_products_home_page_list', options: ['expose' => true], methods: ['GET'])]
    #[Template('Admin/Pages/productHomePagePosition.html.twig')]
    public function getProductHomePageList(): array
    {
        $products = $this->productHomePageCollector->collect();

        return $this->productHomePageFormatter->format($products);
    }
}
