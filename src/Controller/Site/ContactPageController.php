<?php

declare(strict_types=1);

namespace App\Controller\Site;

use App\Formatter\Site\ContactPageFormatter;
use App\Repository\SettingsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

final class ContactPageController extends AbstractController
{
    /**
     * @var SettingsRepository
     */
    private $settingsRepository;

    private ContactPageFormatter $contactPageFormatter;

    /**
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        SettingsRepository $settingsRepository,
        ContactPageFormatter $contactPageFormatter
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->contactPageFormatter = $contactPageFormatter;
    }

    /**
     * @Route({
     *          "rs": "/kontakt",
     *          "en": "/contact"
     *     },
     *     name="site.contact_page",
     *     methods={"GET"}
     * )
     * @Template("Site/Pages/contactUsPage.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
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
