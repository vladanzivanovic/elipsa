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

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var SliderTextRepository
     */
    private $repository;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param SliderTextRepository  $repository
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        SliderTextRepository $repository
    ) {
        $this->parameterBag = $parameterBag;
        $this->repository = $repository;
    }

    /**
     * @param ParameterBag    $bag
     * @param SliderText|null $sliderText
     *
     * @return SliderText
     */
    public function parse(ParameterBag $bag, SliderText $sliderText = null): SliderText
    {
        if (!$sliderText instanceof SliderText) {
            $sliderText = new SliderText();
            $sliderText->setIsActive(false);
        }

        $this->setLocale($bag, $sliderText);

        return $sliderText;
    }

    private function setLocale(ParameterBag $bag, Slidertext $sliderText)
    {
        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        foreach ($locales as $locale => $lagBag) {
            $trans = new SliderTextTranslation();

            if (null !== $sliderText->getId()) {
                $trans = $sliderText->getByLocale($locale);
            }

            $trans->setDescription($lagBag->get('description'));
            $trans->setLink($lagBag->get('link'));
            $trans->setLocale($locale);
            $trans->setSliderText($sliderText);

            $sliderText->addSliderTextTranslation($trans);
        }
    }
}