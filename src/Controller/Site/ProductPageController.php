<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\ProductPageCollector;
use App\Entity\ProductTranslation;
use App\Formatter\Site\ProductPageFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductPageController extends AbstractController
{
    /**
     * @var ProductPageCollector
     */
    private $pageCollector;
    /**
     * @var ProductPageFormatter
     */
    private $pageFormatter;

    /**
     * ProductPageController constructor.
     *
     * @param ProductPageCollector $pageCollector
     * @param ProductPageFormatter $pageFormatter
     */
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
        $collection = $this->pageCollector->collect($productTranslation, $request->getLocale());

        return $this->pageFormatter->formatResponse($collection);
    }
}