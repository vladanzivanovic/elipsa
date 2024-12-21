<?php

declare(strict_types=1);

namespace App\View;

use App\Entity\Youtube;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class YoutubeView
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {}

    public function view(Youtube $youtube): array
    {
        return [
            'Id' => $youtube->getId(),
            'AdsId' => $youtube->getProduct()->getId(),
            'YouTubeId' => $youtube->getYoutubeId(),
            'Title' => $youtube->getTitle(),
            'images' => [
                'original' => $youtube->getImages(),
                'cropped' => [
                    'main' => $this->router->generate(
                        'app.youtube_image_show',
                        ['entity' => 'product', 'name' => $youtube->getYoutubeId(), 'filter' => 'youtube_image'],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                    'thumbnail' => $this->router->generate(
                        'app.youtube_image_show',
                        ['entity' => 'product', 'name' => $youtube->getYoutubeId(), 'filter' => 'youtube_image_thumb'],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                ],
            ],
            'link' => 'https://www.youtube.com/watch?v='.$youtube->getYoutubeId().'&hd=1&rel=0&mute=1',
        ];
    }
}
