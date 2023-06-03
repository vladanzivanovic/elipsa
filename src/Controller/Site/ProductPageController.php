<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\ProductPageCollector;
use App\Entity\ProductTranslation;
use App\Formatter\Site\ProductPageFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

final class ProductPageController extends AbstractController
{
    private ProductPageCollector $pageCollector;

    private ProductPageFormatter $pageFormatter;

    public function __construct(
        ProductPageCollector $pageCollector,
        ProductPageFormatter $pageFormatter
    ) {
        $this->pageCollector = $pageCollector;
        $this->pageFormatter = $pageFormatter;
    }

    /**
     * @Template("Site/Pages/product.html.twig")
     *
     * @param ProductTranslation $productTranslation
     * @param Request            $request
     *
     * @return array
     */
    public function index(ProductTranslation $productTranslation, Request $request): array
    {
        $locale = $request->getSession()->get('_locale');

        $collection = $this->pageCollector->collect($productTranslation, $locale, $this->getUser());

        return $this->pageFormatter->formatResponse($collection);
    }
}
