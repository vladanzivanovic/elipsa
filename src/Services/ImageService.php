<?php

namespace App\Services;

//ini_set('memory_limit', '512M');

use App\Entity\Image;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Liip\ImagineBundle\Service\FilterService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

class ImageService
{
    private $fs;
    private $fileTypes;
    private $tmpDir;
    private $cacheManager;
    /**
     * @var FilterService
     */
    private $filterService;
    /**
     * @var DataManager
     */
    private $dataManager;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @param Filesystem            $filesystem
     * @param ParameterBagInterface $parameterBag
     * @param CacheManager          $cacheManager
     * @param FilterService         $filterService
     * @param RouterInterface       $router
     * @param DataManager           $dataManager
     * @param FilterManager         $filterManager
     */
    public function __construct(
        Filesystem $filesystem,
        ParameterBagInterface $parameterBag,
        CacheManager $cacheManager,
        FilterService $filterService,
        RouterInterface $router,
        DataManager $dataManager,
        FilterManager $filterManager
    ) {
        $this->fs = $filesystem;
        $this->fileTypes = $parameterBag->get('file_types');
        $this->tmpDir = $parameterBag->get('upload_tmp_dir');
        $this->cacheManager = $cacheManager;
        $this->filterService = $filterService;
        $this->dataManager = $dataManager;
        $this->parameterBag = $parameterBag;
        $this->filterManager = $filterManager;
    }

    public function moveImageToFinalPath($file, $destination, $newName = null)
    {
        if(!($file instanceof UploadedFile)) {
            $file['file'] = substr($file['file'], 1);

            $file = $this->setFileObject($file);

            if (null === $file) {
                return false;
            }
        }

        $file->move($destination, $newName);
        $this->deleteTmpImage($file->getClientOriginalName());
    }

    /**
     * @param array $image
     *
     * @return UploadedFile
     */
    public function setFileObject(array $image): UploadedFile
    {
        return new UploadedFile($image['file'], $image['fileName'], null, null, true);
    }

    /**
     * Check if directory exist and thumb inside
     * If not exist then create new folders
     *
     * @return bool
     * @throws IOException
     */
    public function checkExistsAndCreateFolder($folder, $setThumb = false)
    {
        if (!$this->fs->exists($folder)) {
            $this->fs->mkdir($folder, 0775);
        }
        if (true === $setThumb && !$this->fs->exists($folder . '/thumb'))
            $this->fs->mkdir($folder . '/thumb', 0775);

        return true;
    }

    /**
     * @param UploadedFile $file
     */
    public function deleteImage(UploadedFile $file): void
    {
        $path = $file->getPathname();

        if ($this->fs->exists($path)) {
            $this->fs->remove($path);
            $this->cacheManager->remove(str_replace($this->parameterBag->get('upload_dir'), '', $path));
        }
    }

    /**
     * @param array $images
     *
     * @return void
     */
    public function deleteImages(array $images): void
    {
        $rootDir = $this->parameterBag->get('upload_dir');
        $imageDir = $this->parameterBag->get('upload_image_dir');

        /** @var Image $image */
        foreach ($images as $image) {
            $this->deleteImage($this->setFileObject(['file' => $rootDir.$imageDir.$image->getName(), 'fileName' => $image->getName()]));
        }
    }

    /**
     * @param        $file
     * @param string $path
     *
     * @return File
     */
    public function uploadToPath($file, string $path): File
    {
        if (!$file instanceof UploadedFile) {
            // TODO set instance for UploadedFile
        }

        try {
            $movedFile = $file->move($path, $file->getClientOriginalName());
        } catch (\Throwable $throwable) {
            dd($throwable);
        }

        return $movedFile;
    }

    private function deleteTmpImage($file):void
    {
        $path = $this->tmpDir.DIRECTORY_SEPARATOR.$file;

        if($file instanceof UploadedFile) {
            $path = $file->getPath();
        }

        if ($this->fs->exists($path)) {
            $this->fs->remove($path);
        }
    }
}