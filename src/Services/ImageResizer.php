<?php

declare(strict_types=1);

namespace App\Services;

use Liip\ImagineBundle\Controller\ImagineController;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

final class ImageResizer
{
    private bool|string|int|float|\UnitEnum|array|null $tmpDir;
    private \Liip\ImagineBundle\Service\FilterService $filterService;
    private bool|string|int|float|\UnitEnum|array|null $rootDir;

    public function __construct(
        ParameterBagInterface $parameterBag,
        FilterService $filterService
    ) {
        $this->rootDir = $parameterBag->get('upload_dir');
        $this->tmpDir = $parameterBag->get('upload_tmp_dir');
        $this->filterService = $filterService;
    }

    
    public function moveToFolder(UploadedFile $file, string $folder): void
    {
        /** @var File $movedFile */
        $movedFile = $file->move($this->rootDir.$folder, $file->getClientOriginalName());

        $path = $this->tmpDir.$movedFile->getFilename();

        $this->filterService->getUrlOfFilteredImage($path, 'tmp_images');
    }

    
    public function renderImageWithFilter(string $path, string $filter): BinaryFileResponse
    {
        $url = $this->filterService->getUrlOfFilteredImage($path, $filter);

        $path = parse_url($url, PHP_URL_PATH);

        return new BinaryFileResponse($this->rootDir.$path);
    }
}