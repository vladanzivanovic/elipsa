<?php

declare(strict_types=1);

namespace App\Formatter\Admin;

use App\Entity\Tags;

final class TagEditFormatter
{
    public function format(array $data): array
    {
        $formattedData = [
            'options' => $data['productTagsOptions'],
            'tags' => [],
        ];

        if (isset($data['tags'])) {
            /** @var Tags $tag */
            foreach ($data['tags'] as $tag) {
                $formattedData['tags'][$tag->getLocale()] = $tag;
            }
        }

        return $formattedData;
    }
}
