<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Collector\Admin\SettingsPageCollector;
use App\Formatter\Admin\SettingsEditPageFormatter;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

final class SettingsEditPageController extends AbstractController
{
    public function __construct(
        private readonly SettingsPageCollector $pageCollector,
        private readonly SettingsEditPageFormatter $settingsEditPageFormatter,
    ) {}

    #[Route(path: '/settings', name: 'admin.settings_page', methods: ['GET'])]
    #[Template('Admin/Pages/settingsPage.html.twig')]
    public function getSettings(): array
    {
        $payload = $this->pageCollector->collect();

        return $this->settingsEditPageFormatter->format($payload);
    }
}
