<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Repository\SettingsRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class AboutUsPageController extends AbstractController
{
    private \App\Repository\SettingsRepository $settingsRepository;

    public function __construct(
        SettingsRepository $settingsRepository
    ) {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     *
     * @return string[]
     */
    #[Route(path: [
        'rs' => '/o-nama',
        'en' => '/about-us',
        'ba' => '/o-nama',
    ], name: 'site.about_us_page', methods: ['GET'])]
    #[Template('Site/Pages/aboutUsPage.html.twig')]
    public function renderPage(Request $request)
    {
        $settings = $this->settingsRepository->findOneBy(['locale' => $request->getSession()->get('_locale'), 'slug' => 'ABOUT_US']);

        return [
            'aboutUs' => null !== $settings ? $settings->getValue() : '',
        ];
    }
}
