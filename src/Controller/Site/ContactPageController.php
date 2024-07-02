<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Formatter\Site\ContactPageFormatter;
use App\Repository\SettingsRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ContactPageController extends AbstractController
{
    private ContactPageFormatter $contactPageFormatter;

    public function __construct(
        SettingsRepository $settingsRepository,
        ContactPageFormatter $contactPageFormatter
    ) {
        $this->contactPageFormatter = $contactPageFormatter;
    }

    
    #[Route(path: [
        'rs' => '/kontakt',
        'en' => '/contact',
        'ba' => '/kontakt',
    ], name: 'site.contact_page', methods: ['GET'])]
    #[Template('Site/Pages/contactUsPage.html.twig')]
    public function renderPage(Request $request): array
    {
        return $this->contactPageFormatter->format($request->getLocale());

//        $settings = $this->settingsRepository->getSettingsForContactPage();
//        $formatted = [];
//
//        foreach ($settings as $setting) {
//            $formatted[$setting['slug']] = $setting['value'];
//        }
//
//        return $formatted;
    }
}
