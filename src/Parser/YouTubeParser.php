<?php
declare(strict_types=1);

namespace App\Parser;

use App\Entity\Product;
use App\Entity\Youtube;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class YouTubeParser
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {

    }
    public function parse(array $youtubeItem): ?Youtube
    {
        if(isset($youtube['isDeleted']) && true === $youtube['isDeleted']) {
            return null;
        }

        $entity = $this->create();
        $entity->setYoutubeid($youtubeItem['YouTubeId']);
        $entity->setTitle($youtubeItem['Title']);
        $entity->setImages($youtubeItem['Thumbnails']);

        $this->storeThumbnail($youtubeItem);

        return $entity;
    }

    private function create(): Youtube
    {
        return new Youtube();
    }

    private function storeThumbnail(array $youtubeItem): void
    {
        $youtubeResponse = $this->httpClient->request(Request::METHOD_GET, $youtubeItem['Thumbnails']['maxres']['url']);

        $fileHandler = fopen(__DIR__.'/../../storage/youtube/'.$youtubeItem['YouTubeId'].'.jpg', 'w');

        foreach ($this->httpClient->stream($youtubeResponse) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }
    }
}
