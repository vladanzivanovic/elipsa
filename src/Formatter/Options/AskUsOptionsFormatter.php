<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use App\Entity\AskUs;
use App\Helper\ConstantsHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class AskUsOptionsFormatter
{
    private ConstantsHelper $constantsHelper;

    private TranslatorInterface $translator;

    public function __construct(
        ConstantsHelper $constantsHelper,
        TranslatorInterface $translator
    ){
        $this->constantsHelper = $constantsHelper;
        $this->translator = $translator;
    }

    public function format(string $locale): array
    {
        $constants = $this->constantsHelper->getClassConstants(AskUs::class, 'ANSWER');

        $options = [];

        foreach ($constants as $value) {
            $options[] = [
                'title' => $this->translator->trans('contact.form.answer_'.$value, [], 'messages', $locale),
                'value' => $value
            ];
        }

        return $options;
    }
}
