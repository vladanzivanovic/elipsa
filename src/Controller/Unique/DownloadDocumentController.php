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
use Symfony\Component\Routing\Attribute\Route;

class DownloadDocumentController extends AbstractController
{

    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;

    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $parameterBag,
        ImageResizer $imageResizer,
        ImageRepository $imageRepository
    ) {
        $this->parameterBag = $parameterBag;
    }

    
    #[Route(path: '/download-doc/{id}', methods: ['GET'], name: 'app.download_doc', options: ['expose' => true])]
    public function getImage(Image $image): BinaryFileResponse
    {
        $rootDir = $this->parameterBag->get('upload_dir');
        $uploadDir = $this->parameterBag->get('upload_image_dir');

        return new BinaryFileResponse($rootDir.$uploadDir.$image->getOriginalName(), BinaryFileResponse::HTTP_OK, [], true, ResponseHeaderBag::DISPOSITION_ATTACHMENT);

    }
}
