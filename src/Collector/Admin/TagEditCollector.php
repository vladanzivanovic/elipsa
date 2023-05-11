<?php

declare(strict_types=1);

namespace App\Collector\Admin;

use App\Entity\Tags;
use App\Repository\TagsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TagEditCollector
{
    private TranslatorInterface $translator;

    private TagsRepository $tagsRepository;

    private RequestStack $requestStack;

    private string $locales;

    public function __construct(
        TranslatorInterface $translator,
        TagsRepository $tagsRepository,
        RequestStack $requestStack,
        string $locales
    ) {
        $this->translator = $translator;
        $this->tagsRepository = $tagsRepository;
        $this->locales = $locales;
        $this->requestStack = $requestStack;
    }

    public function collect(?Tags $tag = null): array
    {
        $data = [
            'productTagsOptions' => [
                [
                    'value' => Tags::PRODUCT_TYPE_SEASON,
                    'title' => $this->translator->trans(Tags::PRODUCT_TYPE_SEASON),
                ],
                [
                    'value' => Tags::PRODUCT_TYPE_COLLECTION,
                    'title' => $this->translator->trans(Tags::PRODUCT_TYPE_COLLECTION),
                ],
            ]
        ];

        if (null !== $tag) {
            $relatedType = $this->requestStack->getMainRequest()->attributes->get('_route') === 'admin.edit_blog_tag_page' ? Tags::TYPE_BLOG : Tags::TYPE_PRODUCT;

            $locales = explode('|', $this->locales);

            $data['tags'] = $this->tagsRepository->getByMainSlugAndLocales($tag->getMainSlug(), $locales, $relatedType);
        }

        return $data;
    }
}
