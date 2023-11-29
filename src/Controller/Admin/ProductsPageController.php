<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Formatter\Admin\ProductsPageResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class ProductsPageController extends AbstractController
{
    private ProductsPageResponseFormatter $productsPageResponseFormatter;

    public function __construct(
        ProductsPageResponseFormatter $productsPageResponseFormatter
    ) {
        $this->productsPageResponseFormatter = $productsPageResponseFormatter;
    }

    /**
     * @Route("/", name="admin.dashboard", methods={"GET"})
     * @Template("Admin/Pages/dashboard.html.twig")
     *
     * @return array
     */
    public function index(): array
    {
        return $this->productsPageResponseFormatter->format();
    }
}
