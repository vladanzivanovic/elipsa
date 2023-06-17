<?php
declare(strict_types=1);

namespace App\Parser;

use App\Entity\Product;
use App\Entity\Youtube;

class YouTubeParser
{
    public function parse(array $youtubeItem): ?Youtube
    {
        if(isset($youtube['isDeleted']) && true === $youtube['isDeleted']) {
            return null;
        }

        $entity = $this->create();
        $entity->setYoutubeid($youtubeItem['YouTubeId']);
        $entity->setTitle($youtubeItem['Title']);
        $entity->setImages($youtubeItem['Thumbnails']);

        return $entity;
    }

    private function create(): Youtube
    {
        return new Youtube();
    }
}
