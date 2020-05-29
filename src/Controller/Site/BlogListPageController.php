<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\BlogListPageCollector;
use App\Formatter\Site\BlogPageResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogListPageController extends AbstractController
{
    /**
     * @var BlogListPageCollector
     */
    private $pageCollector;
    /**
     * @var BlogPageResponseFormatter
     */
    private $pageFormatter;

    /**
     * @param BlogListPageCollector     $pageCollector
     * @param BlogPageResponseFormatter $pageFormatter
     */
    public function __construct(
        BlogListPageCollector $pageCollector,
        BlogPageResponseFormatter $pageFormatter
    ) {
        $this->pageCollector = $pageCollector;
        $this->pageFormatter = $pageFormatter;
    }

    /**
     * @Route("/blog/{page}/{tag}", methods={"GET"}, requirements={"page": "\d+"}, name="site.blog_list_page", defaults={"page": 1, "tag": null}, options={"expose": true})
     * @Template("Site/Pages/blog.html.twig")
     *
     * @param Request     $request
     * @param int         $page
     * @param string|null $tag
     *
     * @return array
     */
    public function index(Request $request, int $page, ?string $tag): array
    {
        $collection = $this->pageCollector->collect($request->getLocale(), $page);

        return $this->pageFormatter->formatResponse($collection);
    }
}