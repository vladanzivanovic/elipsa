<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\SliderText;
use App\Entity\SliderTextTranslation;
use App\Repository\SliderTextRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class SliderTextEditRequestParser
{
    use ParserTrait;

    private ParameterBagInterface $parameterBag;

    private SliderTextRepository $repository;

    private array $locales;

    public function __construct(
        ParameterBagInterface $parameterBag,
        SliderTextRepository $repository,
        string $locales
    ) {
        $this->parameterBag = $parameterBag;
        $this->repository = $repository;
        $this->locales = explode('|', $locales);
    }

    /**
     * @param SliderText|null $sliderText
     *
     */
    public function parse(ParameterBag $bag, SliderText $sliderText = null): SliderText
    {
        if (!$sliderText instanceof SliderText) {
            $sliderText = new SliderText();
            $sliderText->setIsActive(false);
        }

        $sliderText->setPosition($bag->get('position'));

        $this->setLocale($bag, $sliderText);

        return $sliderText;
    }

    private function setLocale(ParameterBag $bag, Slidertext $sliderText): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $bag->all($locale);

            $trans = new SliderTextTranslation();

            if (null !== $sliderText->getId()) {
                $trans = $sliderText->getByLocale($locale);
            }

            $trans->setTitle($transCollection['title']);
            $trans->setDescription($transCollection['description']);
            $trans->setLink($transCollection['link']);
            $trans->setLocale($locale);
            $trans->setSliderText($sliderText);

            $sliderText->addSliderTextTranslation($trans);
        }
    }
}
