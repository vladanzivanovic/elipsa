<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\HomePageCollector;
use App\Formatter\Site\HomePageResponseFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class HomePageController extends AbstractController
{
    private HomePageCollector $pageCollectors;

    private HomePageResponseFormatter $responseFormatter;

    public function __construct(
        HomePageCollector $pageCollectors,
        HomePageResponseFormatter $responseFormatter
    ) {
        $this->pageCollectors = $pageCollectors;
        $this->responseFormatter = $responseFormatter;
    }

    
    #[Route(path: '/', name: 'site.home_page', options: ['expose' => true], methods: ['GET'])]
    #[Template('Site/Pages/home.html.twig')]
    public function index(Request $request): array
    {
        $locale = $request->getLocale();

        $data = $this->pageCollectors->collect($locale, $this->getUser());

        return $this->responseFormatter->formatResponse($data, $locale, $this->getUser());
    }
}
