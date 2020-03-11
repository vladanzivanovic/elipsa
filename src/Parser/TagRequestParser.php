<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\ProductColor;
use App\Entity\ProductTags;
use App\Repository\ProductColorRepository;
use App\Repository\ProductTagsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

final class TagRequestParser
{
    use ParserTrait;

    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * @var ProductTagsRepository
     */
    private $tagsRepository;

    /**
     * TagRequestParser constructor.
     *
     * @param ParameterBagInterface $parameterBag
     * @param ProductTagsRepository $tagsRepository
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        ProductTagsRepository $tagsRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->tagsRepository = $tagsRepository;
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

        $collection = new ArrayCollection();

        foreach ($locales as $locale => $langBag) {
            $item = new ProductTags();

            if (true === $isEdit) {
                $item = $this->tagsRepository->findOneBy(['mainSlug' => $mainSlug, 'locale' => $locale]);
            }

            $item->setLabel($bag->get($locale.'_title'));
            $item->setMainSlug($mainSlug);
            $item->setLocale($locale);

            $collection->add($item);
        }

        return $collection;
    }
}