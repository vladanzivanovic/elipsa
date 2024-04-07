<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Repository\CareerDescriptionTranslationRepository;

final class JobPageRouterFormatter
{
    private \App\Repository\CareerDescriptionTranslationRepository $descriptionTranslationRepository;

    public function __construct(
        CareerDescriptionTranslationRepository $descriptionTranslationRepository
    ) {
        $this->descriptionTranslationRepository = $descriptionTranslationRepository;
    }

    
    public function localeFormatter(string $slug, string $locale): string
    {
        $fromTrans = $this->descriptionTranslationRepository->findOneBy(['slug' => $slug]);

        $toTrans = $fromTrans->getCareerDescription()->getTranslationByLocale($locale);

        return $toTrans->getSlug();
    }
}