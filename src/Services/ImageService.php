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
    private \Symfony\Component\Filesystem\Filesystem $fs;
    private bool|string|int|float|\UnitEnum|array|null $tmpDir;
    private \Liip\ImagineBundle\Imagine\Cache\CacheManager $cacheManager;
    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;

    /**
     * @param FilterService         $filterService
     * @param RouterInterface       $router
     * @param DataManager           $dataManager
     * @param FilterManager         $filterManager
     */
    public function __construct(
        Filesystem $filesystem,
        ParameterBagInterface $parameterBag,
        CacheManager $cacheManager
    ) {
        $this->fs = $filesystem;
        $this->tmpDir = $parameterBag->get('upload_tmp_dir');
        $this->cacheManager = $cacheManager;
        $this->parameterBag = $parameterBag;
    }

    public function moveImageToFinalPath($file, $destination, $newName = null)
    {
        if(!($file instanceof UploadedFile)) {
            $file['file'] = substr($file['file'], 1);

            $file = $this->setFileObject($file);
        }

        $file->move($destination, $newName);
        $this->deleteTmpImage($file->getClientOriginalName());
    }

    
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
        if (true === $setThumb && !$this->fs->exists($folder . '/thumb')) {
            $this->fs->mkdir($folder . '/thumb', 0775);
        }

        return true;
    }

    public function deleteImage(UploadedFile $file): void
    {
        $path = $file->getPathname();

        if ($this->fs->exists($path)) {
            $this->fs->remove($path);
            $this->cacheManager->remove(str_replace($this->parameterBag->get('upload_dir'), '', $path));
        }
    }

    
    public function deleteImages(array $images): void
    {
        $this->parameterBag->get('upload_dir');
        $this->parameterBag->get('upload_image_dir');

        /** @var Image $image */
        foreach ($images as $image) {
            $this->deleteImage($image->getFile());
        }
    }

    /**
     * @param        $file
     *
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
