<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Description;
use App\Repository\DescriptionRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class DescriptionRequestParser
{
    use ParserTrait;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var DescriptionRepository
     */
    private $descriptionRepository;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param DescriptionRepository $descriptionRepository
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        DescriptionRepository $descriptionRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->descriptionRepository = $descriptionRepository;
    }

    /**
     * @param ParameterBag $bag
     *
     * @return void
     * @throws \Doctrine\ORM\ORMException
     */
    public function parse(ParameterBag $bag): void
    {
        $languages = $this->setLanguageArray($this->parameterBag, $bag);

        foreach ($languages as $locale => $langBag) {
            $description = $this->descriptionRepository->findOneBy(['type' => $bag->get('type'), 'locale' => $locale]);

            if (!$description instanceof Description) {
                $description = new Description();
                $this->descriptionRepository->persist($description);
            }

            $description->setDescription($bag->get($locale.'_description'))
                ->setLocale($locale)
                ->setType((int) $bag->get('type'));
        }
    }
}