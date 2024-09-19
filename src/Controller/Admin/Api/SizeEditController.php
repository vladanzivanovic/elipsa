<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\ProductSize;
use App\Handler\SizeHandler;
use App\Parser\SizeRequestParser;
use App\Request\Dto\Admin\SizeEditRequestDto;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SizeEditController extends AbstractController
{
    public function __construct(
        private readonly SizeRequestParser $requestParser,
        private readonly SizeHandler $sizeHandler,
        private readonly TranslatorInterface $translator
    ) {}

    /**
     *
     *
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route(path: '/api/add-size', name: 'admin.add_size_api', options: ['expose' => true], methods: ['POST'])]
    public function insert(SizeEditRequestDto $sizeEditRequestDto)
    {
        $size = $this->requestParser->parse($sizeEditRequestDto);

        $this->sizeHandler->save($size);

        $sizeEditRequestDto->session->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, Response::HTTP_CREATED);
    }

    /**
     *
     *
     * @return JsonResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/api/edit-size/{id}', name: 'admin.edit_size_api', options: ['expose' => true], methods: ['PUT'])]
    public function update(SizeEditRequestDto $sizeEditRequestDto, ProductSize $productSize)
    {
        $productSize = $this->requestParser->parse($sizeEditRequestDto, $productSize);

        $this->sizeHandler->save($productSize);

        $sizeEditRequestDto->session->getFlashBag()->add('message', $this->translator->trans('data.success_send'));

        return $this->json(null, Response::HTTP_CREATED);
    }
}
