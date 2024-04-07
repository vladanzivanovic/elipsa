<?php

declare(strict_types=1);

namespace App\Formatter\Options;

use App\Entity\Promotion;
use App\Helper\ConstantsHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PromotionTypeOptions
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
    public function format(): array
    {
        $types = $this->constantsHelper->getClassConstants(Promotion::class, 'TYPE');

        $formattedTypes = [];

        foreach ($types as $type) {
            $formattedTypes[] = [
                'title' => $this->translator->trans('promotion.type.'.$type),
                'value' => $type,
            ];
        }

        return $formattedTypes;
    }
}
