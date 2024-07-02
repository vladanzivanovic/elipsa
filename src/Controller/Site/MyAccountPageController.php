<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Formatter\Site\MyAccountFormatter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class MyAccountPageController extends AbstractController
{
    public function __construct(
        private readonly MyAccountFormatter $accountFormatter
    ) {}

    
    #[Route(path: ['rs' => '/moj-nalog', 'en' => '/my-account', 'ba' => '/moj-racun'], name: 'site.account_page', methods: ['GET'])]
    #[Template('Site/Pages/myAccountPage.html.twig')]
    public function renderPage(Request $request): array
    {
        $user = $this->getUser();
        $locale = $request->getSession()->get('_locale');
        $country = $request->attributes->get('_country');

        return $this->accountFormatter->formatResponse($user, $locale, $country);
    }
}
