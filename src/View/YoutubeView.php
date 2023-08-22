<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Youtube;

final class YoutubeView
{
    public function view(Youtube $youtube): array
    {
        return [
            'Id' => $youtube->getId(),
            'AdsId' => $youtube->getProduct()->getId(),
            'YouTubeId' => $youtube->getYoutubeId(),
            'Title' => $youtube->getTitle(),
            'Thumbnails' => $youtube->getImages(),
            'link' => 'https://www.youtube.com/watch?v='.$youtube->getYoutubeId()
        ];
    }
}
