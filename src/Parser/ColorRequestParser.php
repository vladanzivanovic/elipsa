<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\ProductColor;
use App\Repository\ProductColorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class ColorRequestParser
{
    use ParserTrait;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var ProductColorRepository
     */
    private $colorRepository;

    /**
     * ColorRequestParser constructor.
     *
     * @param ParameterBagInterface  $parameterBag
     * @param ProductColorRepository $colorRepository
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        ProductColorRepository $colorRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->colorRepository = $colorRepository;
    }

    /**
     * @param ParameterBag $bag
     * @param string       $mainSlug
     * @param bool         $isEdit
     *
     * @return ArrayCollection
     */
    public function parse(ParameterBag $bag, string $mainSlug, bool $isEdit = false): ArrayCollection
    {
        $locales = $this->setLanguageArray($this->parameterBag, $bag);

        $colors = new ArrayCollection();

        foreach ($locales as $locale => $langBag) {
            $color = new ProductColor();

            if (true === $isEdit) {
                $color = $this->colorRepository->findOneBy(['mainSlug' => $mainSlug, 'locale' => $locale]);
            }

            $color->setHex($bag->get('color'));
            $color->setName($bag->get($locale.'_title'));
            $color->setMainSlug($mainSlug);
            $color->setLocale($locale);

            $colors->add($color);
        }

        return $colors;
    }
}