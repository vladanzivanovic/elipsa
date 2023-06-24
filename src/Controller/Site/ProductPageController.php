<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\ProductPageCollector;
use App\Entity\ProductTranslation;
use App\Formatter\Site\ProductFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

final class ProductPageController extends AbstractController
{
    private ProductPageCollector $pageCollector;

    private ProductFormatter $pageFormatter;

    public function __construct(
        ProductPageCollector $pageCollector,
        ProductFormatter $pageFormatter
    ) {
        $this->pageCollector = $pageCollector;
        $this->pageFormatter = $pageFormatter;
    }

    /**
     * @Template("Site/Pages/product.html.twig")
     */
    public function index(ProductTranslation $productTranslation, Request $request): array
    {
        $locale = $request->getSession()->get('_locale');

        $collection = $this->pageCollector->collect($productTranslation, $locale, $this->getUser());

        return $this->pageFormatter->formatResponse($collection, $locale);
    }
}
