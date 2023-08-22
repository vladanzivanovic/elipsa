<?php

declare(strict_types=1);

namespace App\Formatter\Site\Router;

use App\Repository\TagTranslationRepository;

final class TagUrlLocalizationFormatter
{
    private TagTranslationRepository $translationRepository;
    public function __construct(
        TagTranslationRepository $translationRepository
    ) {
        $this->translationRepository = $translationRepository;
    }

    public function localeFormatter(string $slug, string $locale): ?string
    {
        $trans = $this->translationRepository->findOneBy(['slug' => $slug]);

        $tag = $trans->getTag();

        $toLocaleTrans = $tag->getByLocale($locale);

        return null !== $toLocaleTrans ? $toLocaleTrans->getSlug() : null;
    }
}
