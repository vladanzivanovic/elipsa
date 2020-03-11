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
    /**
     * @var string
     */
    private $tmpDir;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var FilterService
     */
    private $filterService;
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param ParameterBagInterface $parameterBag
     * @param FilterService         $filterService
     */
    public function __construct(
        ParameterBagInterface $parameterBag,
        FilterService $filterService
    ) {
        $this->parameterBag = $parameterBag;
        $this->rootDir = $parameterBag->get('upload_dir');
        $this->tmpDir = $parameterBag->get('upload_tmp_dir');
        $this->filterService = $filterService;
    }

    /**
     * @param UploadedFile $file
     *
     * @return void
     */
    public function moveToTmpDir(UploadedFile $file): void
    {
        /** @var File $movedFile */
        $movedFile = $file->move($this->rootDir.$this->tmpDir, $file->getClientOriginalName());

        $path = $this->tmpDir.$movedFile->getFilename();

        $this->filterService->getUrlOfFilteredImage($path, 'tmp_images');
    }

    /**
     * @param string $path
     * @param string $filter
     *
     * @return BinaryFileResponse
     */
    public function renderImageWithFilter(string $path, string $filter): BinaryFileResponse
    {
        $url = $this->filterService->getUrlOfFilteredImage($path, $filter);

        $path = parse_url($url, PHP_URL_PATH);

        return new BinaryFileResponse($this->rootDir.$path);
    }
}