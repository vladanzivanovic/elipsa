<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Catalogue;
use App\Entity\CatalogueTranslation;
use App\Repository\CatalogueTranslationRepository;
use App\Services\CatalogImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class CatalogEditRequestParser
{
    use ParserTrait;

    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;

    private \App\Repository\CatalogueTranslationRepository $translationRepository;

    private \App\Services\CatalogImageService $imageService;

    public function __construct(
        ParameterBagInterface $parameterBag,
        CatalogueTranslationRepository $translationRepository,
        CatalogImageService $imageService
    ) {
        $this->parameterBag = $parameterBag;
        $this->translationRepository = $translationRepository;
        $this->imageService = $imageService;
    }

    /**
     * @param Catalogue|null $catalogue
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(ParameterBag $bag, ?Catalogue $catalogue = null): Catalogue
    {
        if (!$catalogue instanceof Catalogue) {
            $catalogue = new Catalogue();
            $catalogue->setStatus(Catalogue::STATUS_PENDING);
        }

        $this->setLocales($bag, $catalogue);

        $this->imageService->setImages($catalogue->getCatalogueTranslations()->first(), json_decode($bag->get('images'), true));

        return $catalogue;
    }

    private function setLocales(ParameterBag $bag, Catalogue $catalogue): void
    {
        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        foreach (array_keys($locales) as $locale) {
            $trans = new CatalogueTranslation();

            if (!is_null($catalogue->getId())) {
                $trans = $this->translationRepository->findOneBy(['catalogue' => $catalogue, 'locale' => $locale]);
            }

            $trans->setTitle($bag->get($locale.'_title'));
            $trans->setLocale($locale);

            $catalogue->addCatalogueTranslation($trans);
        }
    }
}