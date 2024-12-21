<?php
declare(strict_types=1);

namespace App\Parser;

use App\Entity\Youtube;

class YouTubeParser
{
    public function parse(array $youtubeItem): ?Youtube
    {
        if(isset($youtubeItem['isDeleted']) && true === $youtubeItem['isDeleted']) {
            return null;
        }

        $entity = $this->create();
        $entity->setYoutubeid($youtubeItem['YouTubeId']);
        $entity->setTitle($youtubeItem['Title']);
        $entity->setImages($youtubeItem['Thumbnails'] ?? $youtubeItem['images']['original']);

        return $entity;
    }

    private function create(): Youtube
    {
        return new Youtube();
    }
}
