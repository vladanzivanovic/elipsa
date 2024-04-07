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

    private RequestStack $requestStack;

    private string $locales;

    public function __construct(
        TranslatorInterface $translator,
        TagsRepository $tagsRepository,
        RequestStack $requestStack,
        string $locales
    ) {
        $this->translator = $translator;
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
                [
                    'value' => Tags::PRODUCT_TYPE_ATTRIBUTE,
                    'title' => $this->translator->trans(Tags::PRODUCT_TYPE_ATTRIBUTE),
                ],
            ]
        ];

        if ($tag instanceof \App\Entity\Tags) {
            $relatedType = $this->requestStack->getMainRequest()->attributes->get('_route') === 'admin.edit_blog_tag_page' ? Tags::TYPE_BLOG : Tags::TYPE_PRODUCT;

            $locales = explode('|', $this->locales);

            $data['tag'] = $tag;
        }

        return $data;
    }
}
