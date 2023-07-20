<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Tags;
use App\View\TagView;

final class TagEditFormatter
{
    private TagView $tagView;

    public function __construct(
        TagView $tagView
    ) {

        $this->tagView = $tagView;
    }
    public function format(array $data): array
    {
        $formattedData = [
            'options' => $data['productTagsOptions'],
            'tags' => [],
        ];

        if (isset($data['tag'])) {
            $formattedData['tag'] = $this->tagView->view($data['tag']);
        }

        return $formattedData;
    }
}
