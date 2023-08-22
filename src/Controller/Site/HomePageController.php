<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\HomePageCollector;
use App\Formatter\Site\HomePageResponseFormatter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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

    /**
     * @Route("/", name="site.home_page", methods={"GET"}, options={"expose": true})
     * @Template("Site/Pages/home.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function index(Request $request): array
    {
        $locale = $request->getLocale();
        $data = $this->pageCollectors->collect($locale, $this->getUser());

        return $this->responseFormatter->formatResponse($data, $locale);
    }
}
