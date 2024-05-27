<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\CareerDescription;
use App\Entity\CareerDescriptionTranslation;
use App\Entity\Resources\StatusInterface;
use App\Repository\CareerDescriptionTranslationRepository;
use App\Request\Dto\Admin\CareerDescriptionEditRequestDto;
use App\Services\JobImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class JobRequestParser
{
    use ParserTrait;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
        private readonly CareerDescriptionTranslationRepository $descriptionTranslationRepository,
        private readonly JobImageService $imageService,
        private readonly array $locales,
    ) {}

    /**
     * @param CareerDescription|null $careerDescription
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(CareerDescriptionEditRequestDto $careerDescriptionEditRequestDto, CareerDescription $careerDescription = null): CareerDescription
    {
        if (!$careerDescription instanceof CareerDescription) {
            $careerDescription = new CareerDescription();
            $careerDescription->setStatus(StatusInterface::STATUS_PENDING);
        }

        $careerDescription->setAvailableCountries($careerDescriptionEditRequestDto->availableCountries);

        $this->setTranslation($careerDescriptionEditRequestDto->translations, $careerDescription);

        $this->imageService->setImages($careerDescription->getCareerDescriptionTranslations()->first(), $careerDescriptionEditRequestDto->images);

        return $careerDescription;
    }

    private function setTranslation(array $translations, CareerDescription $careerDescription): void
    {
        foreach ($this->locales as $locale) {
            if (!isset($translations[$locale])) {
                continue;
            }

            $transCollection = $translations[$locale];
            $trans = $this->descriptionTranslationRepository->findOneBy(['careerDescription' => $careerDescription, 'locale' => $locale]);

            if (null === $trans) {
                $trans = new CareerDescriptionTranslation();
            }

            $trans->setDescription($transCollection['description']);
            $trans->setTitle($transCollection['title']);
            $trans->setLocale($locale);

            $careerDescription->addCareerDescriptionTranslation($trans);
        }
    }
}
