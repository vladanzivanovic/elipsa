<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\SiteTextCollector;
use App\Formatter\Site\SiteTextFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class SiteTextController extends AbstractController
{
    private SiteTextCollector $siteTextCollector;

    private SiteTextFormatter $siteTextFormatter;

    public function __construct(
        SiteTextCollector $siteTextCollector,
        SiteTextFormatter $siteTextFormatter
    ) {
        $this->siteTextCollector = $siteTextCollector;
        $this->siteTextFormatter = $siteTextFormatter;
    }

    #[Route(path: [
        'rs' => '/kompanija/{type}',
        'en' => '/company/{type}',
        'ba' => '/kompanija/{type}',
    ], name: 'site.company_text', options: ['expose' => true], methods: ['GET'])]
    #[Template('Site/Pages/siteText.html.twig')]
    public function index(string $type, Request $request): array
    {
        $locale = $request->getLocale();

        $siteText = $this->siteTextCollector->collect($type, $locale);

        return $this->siteTextFormatter->format($siteText);

    }
}
