<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Collector\BlogListPageCollector;
use App\Formatter\Site\BlogPageResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogListApiController extends AbstractController
{
    public function __construct(
        private readonly BlogListPageCollector $pageCollector,
        private readonly BlogPageResponseFormatter $pageFormatter,
    ) {}

    /**
     *
     * @param string|null $tag
     *
     */
    #[Route(path: '/api/blog/{page}/{tag}', methods: ['GET'], name: 'site_api.blog_list_page', defaults: ['page' => 1, 'tag' => null], options: ['expose' => true])]
    public function index(Request $request, int $page, ?string $tag): JsonResponse
    {
        $locale = $request->getSession()->get('_locale');

        $collection = $this->pageCollector->collect($locale, $page, $tag);

        return $this->json($this->pageFormatter->formatResponse($collection, $locale));
    }
}
