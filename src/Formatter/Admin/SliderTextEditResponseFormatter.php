<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\SliderText;
use App\Helper\ConstantsHelper;
use App\Repository\ImageRepository;
use App\View\SliderTextView;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SliderTextEditResponseFormatter
{
    private ConstantsHelper $constantsHelper;

    private TranslatorInterface $translator;

    private SliderTextView $sliderTextView;

    public function __construct(
        ConstantsHelper $constantsHelper,
        TranslatorInterface $translator,
        SliderTextView $sliderTextView
    ) {
        $this->constantsHelper = $constantsHelper;
        $this->translator = $translator;
        $this->sliderTextView = $sliderTextView;
    }

    /**
     * @throws \ReflectionException
     */
    public function formatResponse(SliderText $sliderText = null): array
    {
        $response = [
            'positionOptions' => $this->formatPositions(),
        ];

        if ($sliderText instanceof SliderText) {
            $response['payload'] = $this->sliderTextView->editView($sliderText);
        }

        return $response;
    }

    /**
     * @throws \ReflectionException
     */
    private function formatPositions(): array
    {
        $availablePositions = $this->constantsHelper->getClassConstants(SliderText::class, 'POSITION_');

        $formatted = [];

        foreach ($availablePositions as $value) {
            $formatted[] = [
                'title' => $this->translator->trans('banner_text.'.$value),
                'value' => $value
            ];
        }

        return $formatted;
    }
}
