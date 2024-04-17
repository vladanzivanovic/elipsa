<?php

declare(strict_types=1);

namespace App\Collector;

use App\Entity\Tags;
use App\Repository\TagsRepository;

class BlogOptionsCollector
{
    public function __construct(
        private readonly TagsRepository $tagsRepository,
    ){}

    public function collect(): array
    {
        $tags = $this->tagsRepository->findBy(['relatedType' => Tags::TYPE_BLOG]);

        return [
            'tags' => $tags,
        ];
    }
}
