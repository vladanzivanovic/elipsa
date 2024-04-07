<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\ProductColor;
use App\Entity\Tags;
use App\Entity\TagTranslation;
use App\Repository\ProductColorRepository;
use App\Repository\TagsRepository;
use App\Repository\TagTranslationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class TagRequestParser
{
    private TagTranslationRepository $translationRepository;

    private array $locales;

    /**
     * TagRequestParser constructor.
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        TagsRepository $tagsRepository,
        TagTranslationRepository $translationRepository,
        string $locales
    ) {
        $this->locales = explode('|', $locales);
        $this->translationRepository = $translationRepository;
    }

    public function parse(ParameterBag $bag, int $type, ?string $slug): Tags
    {
        $tags = new Tags();

        if (null !== $slug) {
            $tags = $this->getTagBySlug($slug);

            Assert::notNull($tags);
        }

        $tags->setRelatedType($type);

        if (Tags::TYPE_PRODUCT === $type) {
            $tags->setProductType($bag->get('product_type'));
        }

        $this->setLocales($bag, $tags);

        return $tags;
    }

    public function getTagBySlug(string $slug): ?Tags
    {
        $trans = $this->translationRepository->findOneBy(['slug' => $slug]);

        return null !== $trans ? $trans->getTag() : null;
    }

    private function setLocales(ParameterBag $bag, Tags $tags): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $bag->all($locale);
            $trans = $this->translationRepository->findOneBy(['tag' => $tags, 'locale' => $locale]);


            if (null === $trans) {
                $trans = new TagTranslation();
            }

            $trans->setTitle($transCollection['title']);
            $trans->setLocale($locale);

            $tags->addTagTranslation($trans);
        }
    }
}
