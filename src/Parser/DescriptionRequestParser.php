<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Description;
use App\Repository\DescriptionRepository;
use App\Request\Dto\DescriptionRequestDto;

class DescriptionRequestParser
{
    private DescriptionRepository $descriptionRepository;

    private array $locales;

    public function __construct(
        DescriptionRepository $descriptionRepository,
        array $locales
    ) {
        $this->descriptionRepository = $descriptionRepository;
        $this->locales = $locales;
    }

    public function parse(DescriptionRequestDto $descriptionRequestDto): void
    {
        foreach ($this->locales as $locale) {
            $translation = $descriptionRequestDto->translations[$locale];

            $description = $this->descriptionRepository->findOneBy(['type' => $descriptionRequestDto->type, 'locale' => $locale]);

            if (!$description instanceof Description) {
                $description = new Description();
                $this->descriptionRepository->persist($description);
            }

            $description->setDescription($translation->description)
                ->setLocale($locale)
                ->setType($descriptionRequestDto->type);
        }
    }
}
