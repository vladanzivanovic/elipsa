<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\SiteTextCollector;
use App\Formatter\Site\SiteTextFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * @Route({
     *          "rs": "/kompanija/{type}",
     *          "en": "/company/{type}"
     *     },
     *     name="site.company_text",
     *     methods={"GET"},
     *     options={"expose": true}
     * )
     * @Template("Site/Pages/siteText.html.twig")
     */
    public function index(string $type, Request $request): array
    {
        $locale = $request->getLocale();

        $siteText = $this->siteTextCollector->collect($type, $locale);

        return $this->siteTextFormatter->format($siteText);

    }
}
