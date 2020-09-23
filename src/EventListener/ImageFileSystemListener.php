<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Image;
use App\Services\ImageService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ImageFileSystemListener
{
    private $imageService;
    /**
     * @var ParameterBagInterface
     */
    private $bag;

    /**
     * @param ImageService          $imageService
     * @param ParameterBagInterface $bag
     */
    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $bag
    ) {
        $this->imageService = $imageService;
        $this->bag = $bag;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        $this->manageFile($entity);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        $this->manageFile($entity);
    }

    /**
     * @param $entity
     */
    private function manageFile($entity): void
    {
        if (!$entity instanceof Image) {
            return ;
        }

        if (!$entity->getFile() instanceof UploadedFile) {
            return ;
        }

        if(true === $entity->isDeleted()) {
            $this->imageService->deleteImages([$entity->getFile()]);

            return;
        }

        $rootDir = $this->bag->get('upload_dir');
        $uploadDir = $this->bag->get('upload_image_dir');

        $this->imageService->moveImageToFinalPath($entity->getFile(), $rootDir.$uploadDir, $entity->getOriginalName());
    }
}