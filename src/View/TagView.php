<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Tags;

final class TagView
{
    public function view(Tags $tags): array
    {
        $view = [
            'slug' => $tags->getSlug(),
            'main_slug' => $tags->getMainSlug(),
            'label' => $tags->getLabel(),
        ];

        return $view;
    }
}
