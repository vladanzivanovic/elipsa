<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Collector\SettingsCollector;
use App\Entity\User;
use App\Formatter\SettingsFormatter;
use App\View\UserView;

final class UserRegistrationFormatter
{
    private SettingsFormatter $settingsFormatter;

    private SettingsCollector $settingsCollector;

    private UserView $userView;

    public function __construct(
        SettingsFormatter $settingsFormatter,
        SettingsCollector $settingsCollector,
        UserView $userView
    ) {
        $this->settingsFormatter = $settingsFormatter;
        $this->settingsCollector = $settingsCollector;
        $this->userView = $userView;
    }
    public function formatResponse(User $user, string $locale): array
    {
        $settings = $this->settingsCollector->collect('email');

        $view = $this->userView->view($user);

        unset($view['email']);

        $view['locale'] = $locale;

        $view['settings'] = $this->settingsFormatter->formatResponse($settings);

        return $view;
    }
}
