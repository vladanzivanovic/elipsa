<?php

namespace App\Services;

//ini_set('memory_limit', '512M');

use App\Entity\Image;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    private string $tmpDir;

    public function __construct(
        private readonly Filesystem $fs,
        private readonly ParameterBagInterface $parameterBag,
        private readonly CacheManager $cacheManager,
    ) {
        $this->tmpDir = $parameterBag->get('upload_tmp_dir');
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

    /**
     * @param array<string, string> $image
     * @return UploadedFile
     */
    public function setFileObject(array $image): UploadedFile
    {
        return new UploadedFile($image['file'], $image['fileName'], null, null, true);
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
