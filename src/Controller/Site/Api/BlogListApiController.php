<?php

declare(strict_types=1);

namespace App\Controller\Site\Api;

use App\Collector\BlogListPageCollector;
use App\Collector\BlogOptionsCollector;
use App\Formatter\Site\BlogPageResponseFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class BlogListApiController extends AbstractController
{
    public function __construct(
        private readonly BlogListPageCollector $pageCollector,
        private readonly BlogPageResponseFormatter $pageFormatter,
        private readonly BlogOptionsCollector $blogOptionsCollector,
    ) {}

    /**
     *
     * @param string|null $tag
     *
     */
    #[Route(path: '/api/blog/{page}/{tag}', name: 'site_api.blog_list_page', options: ['expose' => true], defaults: ['page' => 1, 'tag' => null], methods: ['GET'])]
    public function index(Request $request, int $page, ?string $tag): JsonResponse
    {
        $locale = $request->getSession()->get('_locale');
        $countryCode = $request->attributes->get('_country');

        $collection = $this->pageCollector->collect($locale, $countryCode, $page, $tag);
        $optionsCollector = $this->blogOptionsCollector->collect();

        return $this->json($this->pageFormatter->formatResponse($collection, $optionsCollector, $tag));
    }
}
