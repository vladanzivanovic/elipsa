<?php

namespace App\Controller\Unique;

use App\Services\ImageResizer;
use App\Services\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

class RenderTmpImagesController extends AbstractController
{
    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;
    private \App\Services\ImageResizer $imageResizer;

    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $parameterBag,
        ImageResizer $imageResizer
    ) {
        $this->parameterBag = $parameterBag;
        $this->imageResizer = $imageResizer;
    }

    
    #[Route(path: '/tmp-image-show/{filter}/{name}', methods: ['GET'], name: 'app.tmp_image_show', defaults: ['filter' => 'tmp_images'])]
    public function getTmpImage(string $name, string $filter): BinaryFileResponse
    {
        $uploadDir = $this->parameterBag->get('upload_tmp_dir');

        return $this->imageResizer->renderImageWithFilter($uploadDir.$name, $filter);
    }
}