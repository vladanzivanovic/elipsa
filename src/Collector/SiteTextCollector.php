<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Description;
use App\Repository\DescriptionRepository;

final class SiteTextCollector
{
    private DescriptionRepository $descriptionRepository;

    private array $siteInfoText;

    public function __construct(
        DescriptionRepository $descriptionRepository,
        array $siteInfoText
    ) {
        $this->descriptionRepository = $descriptionRepository;
        $this->siteInfoText = $siteInfoText;
    }

    public function collect(string $type, string $locale): Description
    {
        $siteTextConfig = array_filter($this->siteInfoText, fn($text)  => $text['slug'][$locale] === $type );

        $siteTextConfig = reset($siteTextConfig);

        return $this->descriptionRepository->findOneBy(['type' => $siteTextConfig['type'], 'locale' => $locale]);
    }
}
