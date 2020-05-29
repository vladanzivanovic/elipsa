<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Collector\BlogListPageCollector;
use App\Collector\ProductPageCollector;
use App\Entity\BlogTranslation;
use App\Entity\ProductTranslation;
use App\Formatter\Site\BlogPageResponseFormatter;
use App\Formatter\Site\ProductPageFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogListApiController extends AbstractController
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
     * @Route("/api/blog/{page}/{tag}", methods={"GET"}, name="site_api.blog_list_page", defaults={"page": 1, "tag": null}, options={"expose": true})
     *
     * @param Request     $request
     * @param int         $page
     * @param string|null $tag
     *
     * @return JsonResponse
     */
    public function index(Request $request, int $page, ?string $tag): JsonResponse
    {
        $collection = $this->pageCollector->collect($request->getLocale(), $page);

        return $this->json($this->pageFormatter->formatResponse($collection));
    }
}