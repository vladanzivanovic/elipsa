<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Collector\Admin\SettingsPageCollector;
use Symfony\Bridge\Twig\Attribute\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SettingsEditPageController extends AbstractController
{
    private SettingsPageCollector $pageCollector;

    public function __construct(
        SettingsPageCollector $pageCollector
    ) {
        $this->pageCollector = $pageCollector;
    }

    #[Route(path: '/settings', name: 'admin.settings_page', methods: ['GET'])]
    #[Template('Admin/Pages/settingsPage.html.twig')]
    public function getSettings(): array
    {
        return $this->pageCollector->collect();
    }
}
