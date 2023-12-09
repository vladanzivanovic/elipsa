<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Description;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SiteTextView
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {

        $this->translator = $translator;
    }

    public function pageView(Description $description): array
    {
        $data = [
            'description' => $description->getDescription(),
            'title' => $this->translator->trans('navi.'.$description->getType()),
        ];

        return ['payload' => $data];
    }
}
