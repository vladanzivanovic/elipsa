<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Collector\MyAccountCollector;
use App\Formatter\Site\MyAccountFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class MyAccountPageController extends AbstractController
{
    private \App\Collector\MyAccountCollector $accountCollector;
    private \App\Formatter\Site\MyAccountFormatter $accountFormatter;

    public function __construct(
        MyAccountCollector $accountCollector,
        MyAccountFormatter $accountFormatter
    ) {
        $this->accountCollector = $accountCollector;
        $this->accountFormatter = $accountFormatter;
    }

    
    #[Route(path: ['rs' => '/moj-nalog', 'en' => '/my-account'], name: 'site.account_page', methods: ['GET'])]
    #[Template('Site/Pages/myAccountPage.html.twig')]
    public function renderPage(Request $request): array
    {
        $user = $this->getUser();
        $locale = $request->getSession()->get('_locale');

        $collection = $this->accountCollector->collect($user, $locale);

//        dd($this->accountFormatter->formatResponse($user, $collection, $request->getLocale()));

        return $this->accountFormatter->formatResponse($user, $collection, $request->getLocale());
    }
}
