<?php

namespace App\Controller\Admin\Api;

use App\Entity\Image;
use App\Services\ImageResizer;
use App\Services\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class SummerNoteImageController extends AbstractController
{
    private bool|string|int|float|\UnitEnum|array|null $uploadImageDir;
    private \Symfony\Component\Routing\RouterInterface $router;
    private \App\Services\ImageService $imageService;
    private \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $bag;

    public function __construct(
        ImageResizer $imageResizer,
        RouterInterface $router,
        ImageService $imageService,
        ParameterBagInterface $bag
    ) {
        $this->router = $router;
        $this->imageService = $imageService;
        $this->bag = $bag;
        $this->uploadImageDir = $bag->get('upload_image_dir');
    }

    /**
     * @return JsonResponse
     */
    #[Route(path: '/api/summernote-image/resize', name: 'admin.summernote_image_resize', methods: ['POST'], options: ['expose' => true])]
    public function uploadImage(Request $request)
    {
        try {
            $entity = $request->request->get('entity');

            /** @var UploadedFile $file */
            $file = $request->files->get('tmp_image');
            $this->imageService->uploadToPath($file, $this->bag->get('upload_dir').$this->uploadImageDir);

            return $this->json([
                'file_url' => $this->router->generate('app.summernote_image_show', ['entity' => $entity, 'name' => $file->getClientOriginalName()]),
                'file_name' => $file->getClientOriginalName(),
            ]);

        } catch (\Throwable $throwable) {
            return $this->json([], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     *
     * @return JsonResponse
     */
    #[Route(path: '/api/remove-summernote-image/{filename}', methods: ['DELETE'], name: 'admin.remove_summernote_image', options: ['expose' => true])]
    public function removeImage(string $filename)
    {
        $file = $this->imageService->setFileObject([
            'file' => $this->bag->get('upload_dir').$this->uploadImageDir.$filename,
            'fileName' => $filename,
        ]);

        if ($file instanceof UploadedFile) {
            $this->imageService->deleteImage($file);
        }

        return $this->json([]);
    }
}
