<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Catalogue;
use App\Entity\CatalogueTranslation;
use App\Repository\CatalogueTranslationRepository;
use App\Request\Dto\Admin\CatalogEditRequestDto;
use App\Services\CatalogImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class CatalogEditRequestParser
{
    use ParserTrait;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly CatalogueTranslationRepository $translationRepository,
        private readonly CatalogImageService $imageService,
        private readonly array $locales,
    ) {}

    public function parse(CatalogEditRequestDto $catalogEditRequestDto, null|Catalogue $catalogue = null): Catalogue
    {
        if (!$catalogue instanceof Catalogue) {
            $catalogue = new Catalogue();
            $catalogue->setStatus(Catalogue::STATUS_PENDING);
        }

        $catalogue->setAvailableCountries($catalogEditRequestDto->availableCountries);

        $this->setLocales($catalogEditRequestDto, $catalogue);

        $this->imageService->setImages($catalogue->getCatalogueTranslations()->first(), $catalogEditRequestDto->images);

        return $catalogue;
    }

    private function setLocales(CatalogEditRequestDto $catalogEditRequestDto, Catalogue $catalogue): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $catalogEditRequestDto->translations[$locale];
            $trans = $this->translationRepository->findOneBy(['catalogue' => $catalogue, 'locale' => $locale]);


            if (null === $trans) {
                $trans = new CatalogueTranslation();
            }

            $trans->setTitle($transCollection['title']);
            $trans->setLocale($locale);

            $catalogue->addCatalogueTranslation($trans);
        }
    }
}
