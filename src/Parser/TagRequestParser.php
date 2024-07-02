<?php

declare(strict_types=1);

namespace App\Parser;

use App\Entity\Tags;
use App\Entity\TagTranslation;
use App\Repository\TagTranslationRepository;
use App\Request\Dto\Admin\TagEditRequestDto;
use Webmozart\Assert\Assert;

final class TagRequestParser
{
    public function __construct(
        private readonly TagTranslationRepository $translationRepository,
        private readonly array $locales,
    ) {}

    public function parse(TagEditRequestDto $tagEditRequestDto, null|string $slug): Tags
    {
        $tags = new Tags();

        if (null !== $slug) {
            $tags = $this->getTagBySlug($slug);

            Assert::notNull($tags);
        }

        $tags->setRelatedType($tagEditRequestDto->tagType);

        if (Tags::TYPE_PRODUCT === $tagEditRequestDto->tagType) {
            $tags->setProductType($tagEditRequestDto->productType);
        }

        $this->setLocales($tagEditRequestDto, $tags);

        return $tags;
    }

    public function getTagBySlug(string $slug): null|Tags
    {
        $trans = $this->translationRepository->findOneBy(['slug' => $slug]);

        return $trans?->getTag();
    }

    private function setLocales(TagEditRequestDto $tagEditRequestDto, Tags $tags): void
    {
        foreach ($this->locales as $locale) {
            $transCollection = $tagEditRequestDto->translations[$locale];
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
