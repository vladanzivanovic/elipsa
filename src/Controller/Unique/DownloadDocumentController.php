<?php

namespace App\Controller\Unique;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Services\ImageResizer;
use App\Services\ImageService;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class DownloadDocumentController extends AbstractController
{

    private $imageService;
    private $parameterBag;
    /**
     * @var ImageResizer
     */
    private $imageResizer;
    /**
     * @var ImageRepository
     */
    private $imageRepository;

    /**
     * @param ImageService          $imageService
     * @param ParameterBagInterface $parameterBag
     * @param ImageResizer          $imageResizer
     * @param ImageRepository       $imageRepository
     */
    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $parameterBag,
        ImageResizer $imageResizer,
        ImageRepository $imageRepository
    ) {
        $this->imageService = $imageService;
        $this->parameterBag = $parameterBag;
        $this->imageResizer = $imageResizer;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @Route("/download-doc/{id}",
     *     methods={"GET"},
     *     name="app.download_doc",
     *     options={"expose": true}
     * )
     *
     * @param Image $image
     *
     * @return BinaryFileResponse
     */
    public function getImage(Image $image): BinaryFileResponse
    {
        $rootDir = $this->parameterBag->get('upload_dir');
        $uploadDir = $this->parameterBag->get('upload_image_dir');

        return new BinaryFileResponse($rootDir.$uploadDir.$image->getOriginalName(), BinaryFileResponse::HTTP_OK, [], true, ResponseHeaderBag::DISPOSITION_ATTACHMENT);

    }
}