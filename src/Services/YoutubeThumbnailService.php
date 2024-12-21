<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Youtube;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class YoutubeThumbnailService
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ImageService $imageService,
    ) {}

    public function downloadThumbnail(Youtube $youtube): void
    {
        $youtubeResponse = $this->httpClient->request(Request::METHOD_GET, $youtube->getImages()['maxres']['url']);

        $fileHandler = fopen(__DIR__.'/../../storage/youtube/'.$youtube->getYoutubeId().'.jpg', 'w');

        foreach ($this->httpClient->stream($youtubeResponse) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }
    }

    public function removeThumbnail(string $youtubeId): void
    {
        try {
            $uploadedFile = $this->imageService->setFileObject([
                'file' => __DIR__ . '/../../storage/youtube/' . $youtubeId . '.jpg',
                'fileName' => $youtubeId . '.jpg',
            ]);

            $this->imageService->deleteImage($uploadedFile);
        } catch (FileNotFoundException $fileNotFoundException){}
    }
}
