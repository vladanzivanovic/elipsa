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
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    public function collect(null|Tags $tag = null): array
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

        if ($tag instanceof Tags) {
            $data['payload'] = $tag;
        }

        return $data;
    }
}
