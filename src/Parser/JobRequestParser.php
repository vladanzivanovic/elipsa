<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Blog;
use App\Entity\BlogHasTags;
use App\Entity\BlogTranslation;
use App\Entity\CareerDescription;
use App\Entity\CareerDescriptionTranslation;
use App\Repository\BlogHasTagsRepository;
use App\Repository\BlogTranslationRepository;
use App\Repository\CareerDescriptionTranslationRepository;
use App\Services\BlogImageService;
use App\Services\JobImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class JobRequestParser
{
    use ParserTrait;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var CareerDescriptionTranslationRepository
     */
    private $descriptionTranslationRepository;

    /**
     * @var JobImageService
     */
    private $imageService;

    /**
     * @param ParameterBagInterface                  $parameterBag
     * @param CareerDescriptionTranslationRepository $descriptionTranslationRepository
     * @param JobImageService                        $imageService
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        CareerDescriptionTranslationRepository $descriptionTranslationRepository,
        JobImageService $imageService
    ) {
        $this->parameterBag = $parameterBag;
        $this->descriptionTranslationRepository = $descriptionTranslationRepository;
        $this->imageService = $imageService;
    }

    /**
     * @param ParameterBag           $bag
     * @param CareerDescription|null $careerDescription
     *
     * @return CareerDescription
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(ParameterBag $bag, CareerDescription $careerDescription = null): CareerDescription
    {
        if (!$careerDescription instanceof CareerDescription) {
            $careerDescription = new CareerDescription();
            $careerDescription->setStatus(CareerDescription::STATUS_PENDING);
        }

        $this->setTranslation($careerDescription, $bag);

        $this->imageService->setImages($careerDescription->getTranslationByLocale('rs'), json_decode($bag->get('images'), true));

        return $careerDescription;
    }

    private function setTranslation(CareerDescription $careerDescription, ParameterBag $bag)
    {
        $languages = $this->setLanguageArray($this->parameterBag, $bag);

        foreach ($languages as $locale => $langBag) {
            $careerTranslation = $this->descriptionTranslationRepository->findOneBy(['careerDescription' => $careerDescription, 'locale' => $locale]);

            if (!$careerTranslation instanceof CareerDescriptionTranslation) {
                $careerTranslation = new CareerDescriptionTranslation();
            }

            $careerTranslation->setTitle($bag->get($locale.'_title'))
                ->setDescription($bag->get($locale.'_description'))
                ->setLocale($locale)
                ->setCareerDescription($careerDescription);

            $careerDescription->addCareerDescriptionTranslation($careerTranslation);
        }
    }
}