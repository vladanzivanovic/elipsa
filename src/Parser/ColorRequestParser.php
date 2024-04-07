<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\ColorTranslation;
use App\Entity\ProductColor;
use App\Repository\ColorTranslationRepository;
use App\Repository\ProductColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class ColorRequestParser
{
    use ParserTrait;

    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;

    private \App\Repository\ProductColorRepository $colorRepository;

    /**
     * ColorRequestParser constructor.
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        ProductColorRepository $colorRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->colorRepository = $colorRepository;
    }

    /**
     * @param ProductColor|null $productColor
     *
     */
    public function parse(ParameterBag $bag, ?ProductColor $productColor = null): ProductColor
    {
        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        if (!$productColor instanceof ProductColor) {
            $productColor = new ProductColor();
            $productColor->setHex($bag->get('color'));
        }

        new ArrayCollection();

        foreach (array_keys($locales) as $locale) {
            $trans = new ColorTranslation();

            if (null !== $productColor->getId()) {
                $trans = $productColor->getByLocale($locale);
            }

            $trans->setTitle($bag->get($locale.'_title'));
            $trans->setLocale($locale);
            $trans->setColor($productColor);

            $productColor->addColorTranslation($trans);
        }

        return $productColor;
    }
}