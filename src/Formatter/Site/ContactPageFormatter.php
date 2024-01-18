<?php

declare(strict_types=1);

namespace App\Formatter\Site;

use App\Collector\SettingsCollector;
use App\Formatter\Options\AskUsOptionsFormatter;
use App\Formatter\SettingsFormatter;

class ContactPageFormatter
{
    private AskUsOptionsFormatter $askUsOptionsFormatter;

    private SettingsCollector $settingsCollector;

    private SettingsFormatter $settingsFormatter;

    public function __construct(
        AskUsOptionsFormatter $askUsOptionsFormatter,
        SettingsCollector $settingsCollector,
        SettingsFormatter $settingsFormatter
    ){
        $this->askUsOptionsFormatter = $askUsOptionsFormatter;
        $this->settingsCollector = $settingsCollector;
        $this->settingsFormatter = $settingsFormatter;
    }

    public function format(string $locale): array
    {
        $settings = $this->settingsCollector->collect('contactPage');

        $data = [
            'answer_options' => $this->askUsOptionsFormatter->format($locale),
            'settings' => $this->settingsFormatter->formatResponse($settings),
        ];

        return $data;
    }
}
