<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collectors\ShopPageCollectors;
use App\Formatter\Site\ShopPageResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ShopPageController extends AbstractController
{
    /**
     * @var ShopPageCollectors
     */
    private $collectors;

    /**
     * @var ShopPageResponseFormatter
     */
    private $formatter;

    /**
     * @param ShopPageCollectors        $collectors
     * @param ShopPageResponseFormatter $formatter
     */
    public function __construct(
        ShopPageCollectors $collectors,
        ShopPageResponseFormatter $formatter
    ) {
        $this->collectors = $collectors;
        $this->formatter = $formatter;
    }

    /**
     * @Route("/proizvodi/{page}/{searchData}", name="site.shop_page", methods={"GET"}, defaults={"page": 1, "searchData": null}, requirements={"searchData": ".*"}, options={"expose": true})
     * @Template("Site/Pages/shop.html.twig")
     *
     * @param Request $request
     * @param int     $page
     * @param string  $searchData
     *
     * @return array
     */
    public function index(Request $request, int $page, ?string $searchData): array
    {
        $data = $this->collectors->collect($request->getLocale(), $page, $searchData);

        return $this->formatter->formatResponse($data);
    }
}