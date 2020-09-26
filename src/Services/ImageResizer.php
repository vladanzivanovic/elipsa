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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
     * @param string       $folder
     *
     * @return void
     */
    public function moveToFolder(UploadedFile $file, string $folder): void
    {
        /** @var File $movedFile */
        $movedFile = $file->move($this->rootDir.$folder, $file->getClientOriginalName());

        $path = $this->tmpDir.$movedFile->getFilename();

        $this->filterService->getUrlOfFilteredImage($path, 'tmp_images');
    }

    /**
     * @param string $path
     * @param string $fileName
     * @param string $filter
     *
     * @return BinaryFileResponse
     */
    public function renderImageWithFilter(string $path, string $fileName, string $filter): BinaryFileResponse
    {
        $url = $this->filterService->getUrlOfFilteredImage($path, $filter);

        $path = parse_url($url, PHP_URL_PATH);

        return new BinaryFileResponse($this->rootDir.$path, 200, array(
            'Content-Type' => 'image/jpeg', // Guessing probably all jpegs.
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Content-Length' => filesize($path . $fileName),
            'Expires' => date(DATE_RFC822, strtotime("+2 days")),
            'Cache-Control' => 'public, max-age=10800, pre-check=10800',
            'Pragma' => 'public',
        ), true, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}