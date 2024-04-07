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

    public function __construct(
        AskUsOptionsFormatter $askUsOptionsFormatter,
        SettingsCollector $settingsCollector
    ){
        $this->askUsOptionsFormatter = $askUsOptionsFormatter;
        $this->settingsCollector = $settingsCollector;
    }

    public function format(string $locale): array
    {
        $settings = $this->settingsCollector->collect('contactPage');

        return [
            'answer_options' => $this->askUsOptionsFormatter->format($locale),
            'company_info' => $settings,
        ];
    }
}
