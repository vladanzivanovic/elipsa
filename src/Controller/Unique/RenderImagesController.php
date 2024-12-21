<?php

namespace App\Controller\Unique;

use App\Repository\ImageRepository;
use App\Services\ImageResizer;
use App\Services\ImageService;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Attribute\Route;

class RenderImagesController extends AbstractController
{

    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag;
    private \App\Services\ImageResizer $imageResizer;
    private \App\Repository\ImageRepository $imageRepository;

    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $parameterBag,
        ImageResizer $imageResizer,
        ImageRepository $imageRepository
    ) {
        $this->parameterBag = $parameterBag;
        $this->imageResizer = $imageResizer;
        $this->imageRepository = $imageRepository;
    }

    #[Route(path: '/{entity}-image/{filter}/{name}.jpeg', methods: ['GET'], name: 'app.image_show', requirements: ['entity' => 'product|blog|slider|location|banner|catalog|about-us|job|description'])]
    public function getImage(string $filter, string $name): BinaryFileResponse
    {
        $uploadDir = $this->parameterBag->get('upload_image_dir');

        try {
            $response = $this->imageResizer->renderImageWithFilter($uploadDir . $name, $filter);

            $response->setPublic();

            return $response;
        } catch (NotLoadableException $notLoadableException) {
            $image = $this->imageRepository->findOneBy(['name' => $name]);

            $response = $this->imageResizer->renderImageWithFilter($uploadDir.$image->getOriginalName(), $filter);

            $response->setPublic();

            return $response;

        }
    }

    #[Route(path: '/{entity}-youtube-image/{filter}/{name}.jpeg', methods: ['GET'], name: 'app.youtube_image_show', requirements: ['entity' => 'product'])]
    public function getYoutubeImage(string $filter, string $name): BinaryFileResponse
    {
        $response = $this->imageResizer->renderImageWithFilter('youtube/'.$name.'.jpg', $filter);

        $response->setPublic();

        return $response;
    }

    #[Route(path: '/{entity}-image/summernote_images/{name}', methods: ['GET'], name: 'app.summernote_image_show', requirements: ['entity' => 'product|blog|slider|location|banner|catalog|about-us|job|description'])]
    public function getSummernoteImage(string $name): BinaryFileResponse
    {
        $uploadDir = $this->parameterBag->get('upload_image_dir');

        try {
            $response = $this->imageResizer->renderImageWithFilter($uploadDir . $name, 'summernote_images');

            $response->setPublic();

            return $response;
        } catch (NotLoadableException $notLoadableException) {
            $image = $this->imageRepository->findOneBy(['name' => $name]);

            $response = $this->imageResizer->renderImageWithFilter($uploadDir.$image->getOriginalName(), 'summernote_images');

            $response->setPublic();

            return $response;

        }
    }
}
