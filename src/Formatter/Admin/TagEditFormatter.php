<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\View\TagView;

final class TagEditFormatter
{
    public function __construct(
        private readonly TagView $tagView
    ) {}
    public function format(array $data): array
    {
        $formattedData = [
            'options' => $data['productTagsOptions'],
            'payload' => [],
        ];

        if (isset($data['payload'])) {
            $formattedData['payload'] = $this->tagView->view($data['payload']);
        }

        return $formattedData;
    }
}
