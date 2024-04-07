<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Services\ImageService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;

final class DocumentUploadHandler
{
    private \App\Repository\ImageRepository $imageRepository;

    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $parameterBag,
        ImageRepository $imageRepository
    ) {
        $this->imageRepository = $imageRepository;
    }

    /**
     *
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(ParameterBag $files, int $relatedType): Image
    {
            /** @var UploadedFile $file */
            $file = $files->get('file');

            $doc = $this->imageRepository->findOneBy(['name' => $file->getClientOriginalName(), 'relatedToType' => $relatedType]);

            if (null === $doc) {
                $doc = new Image();
                $doc->setName($file->getClientOriginalName());
                $doc->setOriginalName($file->getClientOriginalName());
                $doc->setDevice(0);
                $doc->setRelatedToType($relatedType);
                $doc->setIsMain(true);

                $this->imageRepository->persist($doc);
            }

            $doc->setFile($file);

            $this->imageRepository->flush();

            return $doc;
    }
}