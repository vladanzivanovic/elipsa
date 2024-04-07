<?php

namespace App\Controller\Admin\Api;

use App\Entity\Image;
use App\Handler\DocumentUploadHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class SummerNoteDocumentController extends AbstractController
{
    private \App\Handler\DocumentUploadHandler $handler;

    private \Symfony\Component\Routing\RouterInterface $router;

    public function __construct(
        DocumentUploadHandler $handler,
        RouterInterface $router
    ) {
        $this->router = $router;
        $this->handler = $handler;
    }

    /**
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    #[Route(path: '/api/summernote-document', name: 'admin.summernote_document_upload', methods: ['POST'], options: ['expose' => '
                                   true'])]
    public function uploadImage(Request $request)
    {
        $document = $this->handler->save($request->files, Image::RELATED_TYPE_DESCRIPTION);

        return $this->json([
            'file_url' => $this->router->generate('app.download_doc', ['id' => $document->getId()], RouterInterface::ABSOLUTE_URL),
            'file_name' => $document->getName(),
        ]);
    }

//    /**
//     * @Route("/api/remove-summernote-document/{filename}", methods={"DELETE"}, name="admin.remove_summernote_document", options={"expose": true})
//     *
//     * @param string $filename
//     *
//     * @return JsonResponse
//     */
//    public function removeDocument(string $filename)
//    {
//        $file = $this->imageService->setFileObject([
//            'file' => $this->bag->get('upload_dir').$this->uploadDocumentDir.$filename,
//            'fileName' => $filename,
//        ]);
//
//        if ($file instanceof UploadedFile) {
//            $this->imageService->deleteImage($file);
//        }
//
//        return $this->json([]);
//    }
}