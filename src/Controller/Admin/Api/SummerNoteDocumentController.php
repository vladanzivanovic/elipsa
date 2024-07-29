<?php

namespace App\Controller\Admin\Api;

use App\Entity\Image;
use App\Handler\DocumentUploadHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

class SummerNoteDocumentController extends AbstractController
{
    public function __construct(
        private readonly DocumentUploadHandler $handler,
        private readonly RouterInterface $router
    ) {}

    /**
     *
     * @return JsonResponse
     * @throws \Doctrine\ORM\ORMException
     */
    #[Route(path: '/api/summernote-document', name: 'admin.summernote_document_upload', options: ['expose' => '
                                   true'], methods: ['POST'])]
    public function uploadImage(Request $request)
    {
        $document = $this->handler->save($request->files, Image::RELATED_TYPE_DESCRIPTION);

        return $this->json([
            'file_url' => $this->router->generate('app.download_doc', ['id' => $document->getId()], RouterInterface::ABSOLUTE_URL),
            'file_name' => $document->getName(),
        ]);
    }
}
