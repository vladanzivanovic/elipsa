<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Repository\CareerDescriptionTranslationRepository;

final class JobPageRouterFormatter
{
    /**
     * @var CareerDescriptionTranslationRepository
     */
    private $descriptionTranslationRepository;

    /**
     * @param CareerDescriptionTranslationRepository $descriptionTranslationRepository
     */
    public function __construct(
        CareerDescriptionTranslationRepository $descriptionTranslationRepository
    ) {
        $this->descriptionTranslationRepository = $descriptionTranslationRepository;
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return string
     */
    public function localeFormatter(string $slug, string $locale): string
    {
        $fromTrans = $this->descriptionTranslationRepository->findOneBy(['slug' => $slug]);

        $toTrans = $fromTrans->getCareerDescription()->getTranslationByLocale($locale);

        return $toTrans->getSlug();
    }
}