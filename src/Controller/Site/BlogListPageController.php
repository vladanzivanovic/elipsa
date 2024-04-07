<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\BlogListPageCollector;
use App\Formatter\Site\BlogPageResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogListPageController extends AbstractController
{
    public function __construct(
        private readonly BlogListPageCollector $pageCollector,
        private readonly BlogPageResponseFormatter $pageFormatter,
    ) {}

    #[Route(path: ['rs' => '/blog/{page}/{tag}', 'en' => '/blog/{page}/{tag}'], name: 'site.blog_list_page', requirements: ['page' => '\d+'], options: ['expose' => true], defaults: ['page' => 1, 'tag' => null], methods: ['GET'])]
    #[Template('Site/Pages/blog.html.twig')]
    public function index(Request $request, int $page, ?string $tag): array
    {
        $locale = $request->getLocale();

        $collection = $this->pageCollector->collect($locale, $page, $tag);

        return $this->pageFormatter->formatResponse($collection, $locale);
    }
}
