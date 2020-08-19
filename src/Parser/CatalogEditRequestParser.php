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

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var CatalogueTranslationRepository
     */
    private $translationRepository;

    /**
     * @var CatalogImageService
     */
    private $imageService;

    /**
     * @param ParameterBagInterface          $parameterBag
     * @param CatalogueTranslationRepository $translationRepository
     * @param CatalogImageService            $imageService
     */
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
     * @param ParameterBag   $bag
     * @param Catalogue|null $catalogue
     *
     * @return Catalogue
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

    /**
     * @param ParameterBag $bag
     * @param Catalogue    $catalogue
     */
    private function setLocales(ParameterBag $bag, Catalogue $catalogue): void
    {
        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        foreach ($locales as $locale => $langBag) {
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