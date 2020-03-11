<?php

namespace App\Controller\Unique;

use App\Entity\Image;
use App\Services\ImageResizer;
use App\Services\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class RenderImagesController extends AbstractController
{
    private $imageService;
    private $parameterBag;
    /**
     * @var ImageResizer
     */
    private $imageResizer;

    /**
     * @param ImageService          $imageService
     * @param ParameterBagInterface $parameterBag
     * @param ImageResizer          $imageResizer
     */
    public function __construct(
        ImageService $imageService,
        ParameterBagInterface $parameterBag,
        ImageResizer $imageResizer
    ) {
        $this->imageService = $imageService;
        $this->parameterBag = $parameterBag;
        $this->imageResizer = $imageResizer;
    }

    /**
     * @Route("/image-show/{slug}/{filter}", methods={"GET"}, name="app.image_show")
     *
     * @param string $filter
     * @param Media  $images
     *
     * @return RedirectResponse
     */
    public function getAdImage(string $filter, Image $image): RedirectResponse
    {
        $uploadDir = $this->parameterBag->get('upload_image_dir');
        $file = $this->imageService->setFileObject(['file' => $uploadDir.$image->getOriginalName(), 'fileName' => $image->getOriginalName()]);

        return $this->imageService->resizeOnFly($file, $filter);
    }

    /**
     * @Route("/tmp-image-show/{name}", methods={"GET"}, name="app.tmp_image_show")
     *
     * @param string $name
     *
     * @return RedirectResponse
     */
    public function getTmpImage(string $name): RedirectResponse
    {
        $rootDir = $this->parameterBag->get('upload_dir');
        $uploadDir = $this->parameterBag->get('upload_tmp_dir');

        $file = $this->imageService->setFileObject(['file' => $rootDir.$uploadDir.$name, 'fileName' => $name]);

        return $this->imageService->resizeOnFly($file, 'tmp_images');
    }

    /**
     * @Route("/tmp-thumb-image/{name}", name="app.tmp_thumb_image", methods={"GET"})
     *
     * @param string $name
     *
     * @return BinaryFileResponse
     */
    public function getTmpImageThumb(string $name): BinaryFileResponse
    {
        $uploadDir = $this->parameterBag->get('upload_tmp_dir');

        return $this->imageResizer->renderImageWithFilter($uploadDir.$name, 'tmp_image_thumb');
    }
}