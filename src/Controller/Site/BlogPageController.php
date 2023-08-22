<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\BlogPageCollector;
use App\Entity\BlogTranslation;
use App\Formatter\Site\BlogDetailedPageResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogPageController extends AbstractController
{
    private BlogPageCollector $pageCollector;

    private BlogDetailedPageResponseFormatter $pageFormatter;

    public function __construct(
        BlogPageCollector $pageCollector,
        BlogDetailedPageResponseFormatter $pageFormatter
    ) {
        $this->pageCollector = $pageCollector;
        $this->pageFormatter = $pageFormatter;
    }

    /**
     * @Route("/blog/{slug}", name="site.blog_detailed_page", methods={"GET"}, options={"expose": true})
     * @ParamConverter("blogTranslation", options={"mapping": {"slug": "alias"}})
     * @Template("Site/Pages/blogPage.html.twig")
     *
     * @param Request         $request
     * @param BlogTranslation $blogTranslation
     *
     * @return array
     */
    public function index(BlogTranslation $blogTranslation, Request $request): array
    {
        $collection = $this->pageCollector->collect($blogTranslation, $request->getLocale());

        return $this->pageFormatter->formatResponse($collection);
    }
}
