<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Services\ImageResizer;
use App\Services\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class ImagesController extends AbstractController
{
    private $imageResizer;
    private $imageService;
    private $router;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    /**
     * ImagesController constructor.
     *
     * @param ImageResizer          $imageResizer
     * @param ImageService          $imageService
     * @param RouterInterface       $router
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        ImageResizer $imageResizer,
        ImageService $imageService,
        RouterInterface $router,
        ParameterBagInterface $parameterBag
    ) {
        $this->imageResizer = $imageResizer;
        $this->imageService = $imageService;
        $this->router = $router;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @Route("/api/image/resize", name="image_resize_on_fly_api", methods={"POST"})
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function uploadTmpImage(Request $request)
    {
        try {
            /** @var UploadedFile $file */
            $file = $request->files->get('tmp_image');
            $this->imageResizer->moveToTmpDir($file);

            return $this->json([
                'file' => $this->router->generate('app.tmp_thumb_image', ['name' => $file->getClientOriginalName()]),
                'fileName' => $file->getClientOriginalName(),
                'isMain' => false,
            ]);

        } catch (\Throwable $throwable) {
            dd($throwable);
            return $this->json([], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/remove-tmp-image/{filename}", methods={"DELETE"}, name="remove_tmp_image")
     *
     * @param string $filename
     *
     * @return JsonResponse
     */
    public function removeTmpImage(string $filename)
    {
        $tmpDir = $this->parameterBag->get('upload_dir').$this->parameterBag->get('upload_tmp_dir');

        $file = $this->imageService->setFileObject([
            'file' => $tmpDir.$filename,
            'fileName' => $filename,
        ]);

        if ($file instanceof UploadedFile) {
            $this->imageService->deleteImage($file);
        }

        return $this->json([]);
    }
}